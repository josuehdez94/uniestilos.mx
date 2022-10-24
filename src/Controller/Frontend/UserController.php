<?php

namespace App\Controller\Frontend;

use App\Entity\User;
use App\Form\Backend\ChangePassword\ChangePasswordFormType;
use App\Form\Frontend\User\UserType;
use App\Generales\Funciones;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @Route("/cliente")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/mi-cuenta/{user_uid}-{crypt}", name="front_user_micuenta", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, $user_uid, $crypt): Response
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $cliente = $entityManager->getRepository(\App\Entity\User::class)->findOneBy(['id' => $this->getUser()]);
        return $this->render('Frontend/User/miCuenta.html.twig', [
            'cliente' => $cliente,
        ]);
    }

    /**
     * @Route("/registro", name="front_user_nuevo", requirements={"crypt": ".+"}, methods={"GET","POST"})
     */
    public function nuevoRegistro(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #encriptar datos
            $encryt = new Funciones();
            $cadena = md5(random_int(-20000, 50000).date('Y-m-d g:i:s'));
            $key = md5(random_int(-50000, 20000).date('Y-m-d g:i:s'));
            $encoded = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
            $clave = md5(date('Y-m-d g:i:s').random_int(-1000, 1000), false);
            $user->setPassword($encoded);
            $user->setTipoUser('C');
            $user->setClaveVerificacion($clave);
            $user->setCrypt($encryt->encriptar($cadena, $key).','.$key);
            $user->setDecrypt($cadena);
            $user->setUid($cadena);
            $entityManager->persist($user);
            $entityManager->flush();
            $message = (new \Swift_Message('Bienvenido a TodoPartes'))
                    ->setFrom('no-contestar@nicenmt.mx')
                    ->setTo($user->getEmail())
                    ->setBody(
                    $this->renderView(
                            'Frontend/User/Correos/validarCuenta.html.twig', [
                                'nombre' => $user->nombreCompleto(),
                                'cadena' => $user->getClaveVerificacion(),
                                'correo' => $user->getEmail()
                            ]
                    ),
                    'text/html'
                    )
            ;

            $mailer->send($message);
            $this->addFlash('Creado', 'Cuenta creada exitosamente, por favor verifica tu cuenta, te hemos enviado una clave a '. $user->getEmail());
            return $this->redirectToRoute('front_user_nuevo');
        }

        return $this->render('Frontend/User/new.html.twig', [
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
    
    /**
     * @Route("/validacion-correo/{cadena}-{correo}", name="frontend_cliente_validacion", methods={"GET", "POST"})
     */
    public function validacionCorreo(Request $request, $cadena, $correo) {
        $entityManager = $this->getDoctrine()->getManager();
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['email' => $correo, 'claveVerificacion' => $cadena]);

        if ($cliente == null) {
            return $this->redirect($this->generateUrl('frontend_home_page'));
        } else {

            if ($cliente->getCuentaValidada() == true) {
                return $this->redirect($this->generateUrl('frontend_home_page'));
            }
        }

        if (!$cliente) {
            $this->addFlash('Editado', 'Ha ocurrido un error al verificar tu cuenta');
            return $this->redirectToRoute('app_login_client');
        } else {
            $cliente->setCuentaValidada(true);
            //$cliente->setClaveVerificacion(null);
            $entityManager->flush();
            $this->addFlash('Creado', 'Tu cuenta ahora esta validada, ya puedes iniciar sesión');
            return $this->redirectToRoute('app_login_client');
        }
    }
}
