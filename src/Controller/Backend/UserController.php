<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\Backend\User\UserType;
use App\Generales\Funciones;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

/**
 * @Route("/backend/admin/usuario")
 */
class UserController extends AbstractController
{
    const CLAVE_API = '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';
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }
    
    /**
     * @Route("", name="backend_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backend/User/indexUsuario.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nuevo", name="backend_user_nuevo", methods={"GET","POST"})
     */
    public function nuevoUsuario(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->getConnection()->beginTransaction();
            try {
                $encryt = new Funciones;
                $cadena = md5(random_int(-20000, 50000).date('Y-m-d g:i:s'));
                $key = md5(random_int(-50000, 20000).date('Y-m-d g:i:s'));
                $clave = md5(date('Y-m-d g:i:s').random_int(-1000, 1000), false);
                $hashedPassword = $passwordHasher->hashPassword($user, 'uniestilos');
                $user->setPassword($hashedPassword);
                $user->setActivo(false);
                $user->setTipoUser('E');
                $user->setClaveVerificacion($clave);
                $user->setCrypt($encryt->encriptar($cadena, $key).','.$key);
                $user->setDecrypt($cadena);
                $user->setUid($cadena);
                $entityManager->persist($user);
                $entityManager->flush();
                //$this->addFlash('Creado', 'Usuario creado exitosamente');
                //return $this->redirectToRoute('backend_user_index');
                $this->processSendingPasswordResetEmail(
                    $form->get('email')->getData(),
                    $entityManager,
                    $mailer
                );
                #comit de todos los flush
                $entityManager->getConnection()->commit();
            }catch (\Exception $e) {
                #rollback de todos los flush
                $entityManager->getConnection()->rollBack();
                throw $this->createNotFoundException($e->getMessage());
            }

            return $this->redirectToRoute('backend_user_index');
        }

        return $this->render('backend/User/nuevoUsuario.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
    
    private function processSendingPasswordResetEmail(string $emailFormData, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);
        // Marks that you are allowed to see the app_check_email page.
        //$this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if(!$user){
           $this->addFlash('reset_password_error', 'El email ingresado no tiene cuenta en uniestilos');
           return $this->redirectToRoute('app_forgot_password_request'); 
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            /*$this->addFlash('reset_password_error', sprintf(
                'There was a problem handling your password reset request - %s',
                 $e->getReason()
            ));*/

            //return $this->redirectToRoute('app_check_email');
            $this->addFlash('reset_password_error', 'ocurrio un error al generar la clave de restablecimiento de contraseña');
            dump($e->getReason());
            exit();
            //return $this->redirectToRoute('app_forgot_password_request');
        }
        $email = (new TemplatedEmail())
            ->from(new Address('josue.hdez_94@outlook.com', 'uniestilos'))
            ->to(new Address($user->getEmail()))
            ->subject('Your password reset request')
            ->htmlTemplate('backend/ResetPassword/email.html.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;
        /* $email = new Mail();
        $email->setFrom("josue.hdez_94@outlook.com", "Uniestilos");
        $email->setSubject("Correo de verificacion email");
        $email->addTo($user->getEmail(), "Uniestilos");
        $email->addContent(
            "text/html", $this->renderView('backend/ResetPassword/email.html.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        );
        $sendgrid = new SendGrid(self::CLAVE_API);
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
        echo 'here';
        exit(); */

        //$mailer->send($email);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dump($e);
        }
        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('backend_user_index');
    }

    /**
     * @Route("/asignar-roles/{usuario_id}", name="backend_user_asignar_roles", methods={"GET","POST"})
     */
    public function asignarRoles(Request $request, EntityManagerInterface $entityManager, $usuario_id): Response
    {
        $usuario = $entityManager->getRepository(User::class)->findOneBy(['id' => $usuario_id]);
        if(!$usuario){
            throw $this->createNotFoundException('Usuario no encontrado');
        }
        $form = $this->createForm(\App\Form\Backend\User\RolesUsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('backend_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/User/asignarRoles.html.twig', [
            'usuario' => $usuario,
            'form' => $form
        ]);
    }
}
