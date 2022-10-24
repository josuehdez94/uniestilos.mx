<?php

namespace App\Controller\Backend;

use App\Entity\Subcategoria;
use App\Form\Backend\Subcategoria\SubcategoriaType;
use App\Repository\CategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/backend/subcategorias")
 */
class SubcategoriaController extends AbstractController
{
    /**
     * @Route("/categoria-{categoria_urlAmigable}", name="backend_subcategoria_categoria", methods={"GET"})
     */
    public function subcategoriasPorCategoria(CategoriaRepository $categoriaRepository, $categoria_urlAmigable): Response
    {
        $categoria = $categoriaRepository->findOneBy(['urlAmigable' => $categoria_urlAmigable]);
        if(!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada');
        }
        return $this->render('backend/Subcategoria/subcategoriasPorCategoria.html.twig', [
            'categoria' => $categoria
        ]);
    }

    /**
     * @Route("/nueva/{categoria_urlAmigable}", name="backend_subcategoria_nueva", methods={"GET","POST"})
     */
    public function nuevaSubcategoria(Request $request, EntityManagerInterface $entityManager, $categoria_urlAmigable, CategoriaRepository $categoriaRepository): Response
    {
        $categoria = $categoriaRepository->findOneBy(['urlAmigable' => $categoria_urlAmigable]);
        if(!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada');
        }
        $subcategoria = new Subcategoria();
        $form = $this->createForm(SubcategoriaType::class, $subcategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subcategoria->setCategoria($categoria);
            $entityManager->persist($subcategoria);
            $entityManager->flush();

            return $this->redirectToRoute('backend_subcategoria_categoria', ['categoria_urlAmigable' => $categoria->getUrlAmigable()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/Subcategoria/nueva.html.twig', [
            'categoria' => $categoria,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/detalles/{id}", name="backend_subcategoria_detalles", methods={"GET"})
     */
    public function detallesSubcategoria(EntityManagerInterface $entityManager, $id): Response
    {
        $subcategoria = $entityManager->getRepository(Subcategoria::class)->findOneBy(['id' => $id]);
        if(!$subcategoria){
            throw $this->createNotFoundException('Subcategoria no encontrada');  
        }
        return $this->render('backend/Subcategoria/detalles.html.twig', [
            'subcategoria' => $subcategoria,
        ]);
    }

    /**
     * @Route("/editar/{id}", name="backend_subcategoria_editar", methods={"GET","POST"})
     */
    public function editarSubcategoria(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $subcategoria = $entityManager->getRepository(Subcategoria::class)->findOneBy(['id' => $id]);
        if(!$subcategoria){
            throw $this->createNotFoundException('Subcategoria no encontrada');
        }
        $form = $this->createForm(SubcategoriaType::class, $subcategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('backend_subcategoria_categoria', ['categoria_urlAmigable' => $subcategoria->getCategoria()->getUrlAmigable()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backend/Subcategoria/edit.html.twig', [
            'subcategoria' => $subcategoria,
            'categoria' => $subcategoria->getCategoria(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="backend_subcategoria_eliminar", methods={"POST", "DELETE"})
     */
    public function eliminarSubcategoria(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $subcategoria = $entityManager->getRepository(Subcategoria::class)->findOneBy(['id' => $id]);
        if(!$subcategoria){
            throw $this->createNotFoundException('Subcategoria no encontrada');
        }
        if ($this->isCsrfTokenValid('delete'.$subcategoria->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subcategoria);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backend_subcategoria_categoria', ['categoria_urlAmigable' => $subcategoria->getCategoria()->getUrlAmigable()], Response::HTTP_SEE_OTHER);
    }
}
