<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#entidades
use App\Entity\User;
#formularios
use App\Form\Backend\ChangePassword\ChangePasswordFormType;

/**
 * Class BackendController
 * @Route("/backend")
 */
class BackendController extends AbstractController
{
    /**
     * @Route("/", name="backend_home_page")
     */
    public function index()
    {
        return $this->render('backend/Default/index.html.twig', [
            'controller_name' => 'BackendController',
        ]);
    }

    /**
     * @Route("/mi-perfil", name="backend_mi_perfil")
     */
    public function miPerfil()
    {
        return $this->render('backend/MiPerfil/miPerfil.html.twig', [

        ]);
    }
    
    /**
     * @Route("/cambiar-contraseña/{user_id}", name="backend_change_contrasena")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $user_id) {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuarioActual = $entityManager->getRepository(User::class)->findOneById($user_id);
            $encoded = $passwordEncoder->encodePassword($this->getUser(), $form->get('plainPassword')->getData());
            $usuarioActual->setPassword($encoded);
            $entityManager->flush();
            $this->addFlash('general', 'Contraseña cambiada correctamente');
            return $this->redirectToRoute('backend_mi_perfil');
        }

        return $this->render('backend/ChangePassword/cambiarContrasena.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
