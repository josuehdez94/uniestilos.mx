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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


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
    public function nuevoRegistro(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #encriptar datos
            $encryt = new Funciones();
            $cadena = md5(random_int(-20000, 50000).date('Y-m-d g:i:s'));
            $key = md5(random_int(-50000, 20000).date('Y-m-d g:i:s'));
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $clave = md5(date('Y-m-d g:i:s').random_int(-1000, 1000), false);
            $user->setPassword($hashedPassword);
            $user->setTipoUser('C');
            $user->setClaveVerificacion($clave);
            $user->setCrypt($encryt->encriptar($cadena, $key).','.$key);
            $user->setDecrypt($cadena);
            $user->setUid($cadena);
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('josue.hdez_94@outlook.com', 'uniestilos'))
                ->to(new Address($user->getEmail()))
                ->subject('Bienbenido(a) a uniestilos.mx')
                ->htmlTemplate('Frontend/Correos/nuevaCuenta.html.twig', [
                    'nombre' => $user->nombreCompleto(),
                    'cadena' => $user->getClaveVerificacion(),
                    'correo' => $user->getEmail()
                ])
                ->context([
                    'nombre' => $user->nombreCompleto(),
                    'cadena' => $user->getClaveVerificacion(),
                    'correo' => $user->getEmail()
                ])
            ;
            $mailer->send($email);
            $this->addFlash('Creado', 'Cuenta creada exitosamente, por favor verifica tu cuenta, te hemos enviado una clave a '. $user->getEmail());
            return $this->redirectToRoute('front_user_nuevo');
        }

        return $this->render('Frontend/User/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/validacion-correo/{cadena}-{correo}", name="frontend_cliente_validacion", methods={"GET", "POST"})
     */
    public function validacionCorreo(Request $request, EntityManagerInterface $entityManager, $cadena, $correo) {
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
