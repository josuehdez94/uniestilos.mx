<?php

namespace App\Controller\Backend;

use App\Entity\RoleSistema;
use App\Form\RoleSistemaType;
use App\Repository\RoleSistemaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/role/sistema")
 */
class RoleSistemaController extends AbstractController
{
    /**
     * @Route("/", name="role_sistema_index", methods={"GET"})
     */
    public function index(RoleSistemaRepository $roleSistemaRepository): Response
    {
        return $this->render('role_sistema/index.html.twig', [
            'role_sistemas' => $roleSistemaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="role_sistema_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $roleSistema = new RoleSistema();
        $form = $this->createForm(RoleSistemaType::class, $roleSistema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($roleSistema);
            $entityManager->flush();

            return $this->redirectToRoute('role_sistema_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('role_sistema/new.html.twig', [
            'role_sistema' => $roleSistema,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="role_sistema_show", methods={"GET"})
     */
    public function show(RoleSistema $roleSistema): Response
    {
        return $this->render('role_sistema/show.html.twig', [
            'role_sistema' => $roleSistema,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="role_sistema_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RoleSistema $roleSistema): Response
    {
        $form = $this->createForm(RoleSistemaType::class, $roleSistema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('role_sistema_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('role_sistema/edit.html.twig', [
            'role_sistema' => $roleSistema,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="role_sistema_delete", methods={"POST"})
     */
    public function delete(Request $request, RoleSistema $roleSistema): Response
    {
        if ($this->isCsrfTokenValid('delete'.$roleSistema->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($roleSistema);
            $entityManager->flush();
        }

        return $this->redirectToRoute('role_sistema_index', [], Response::HTTP_SEE_OTHER);
    }

}
