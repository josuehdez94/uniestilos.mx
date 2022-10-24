<?php

namespace App\Controller\Backend;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#formularios
use App\Form\Backend\Categoria\CategoriaType;

/**
 * @Route("/backend/categorias")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("", name="backend_categoria_index", methods={"GET"})
     */
    public function indexCategoria(CategoriaRepository $categoriaRepository): Response
    {
        return $this->render('backend/Categoria/indexCategoria.html.twig', [
            'categorias' => $categoriaRepository->findBy([], ['id' => 'DESC'])
        ]);
    }

    /**
     * @Route("/nueva", name="backend_categoria_nueva", methods={"GET","POST"})
     */
    public function nuevaCategoria(Request $request): Response
    {
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $categoria->setUsuarioCreador($this->getUser());
            $entityManager->persist($categoria);
            $entityManager->flush();
            $this->addFlash('Creado', 'La Categoria fue creada exitosamente!');
            return $this->redirectToRoute('backend_categoria_index');
        }

        return $this->render('backend/Categoria/nuevaCategoria.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/detalles/{id}", name="backend_categoria_detalles", methods={"GET"})
     */
    public function detallesCategoria($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoria = $entityManager->getRepository(Categoria::class)->findOneById($id);
        if(!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada.');
        }
        return $this->render('backend/Categoria/detallesCategoria.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * @Route("/editar/{id}", name="backend_categoria_editar", methods={"GET","POST"})
     */
    public function editarCategoria(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoria = $entityManager->getRepository(Categoria::class)->findOneById($id);
        if(!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada.');
        }
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoria->setUsuarioEditor($this->getUser());
            $entityManager->flush();
            $this->addFlash('Editado', 'La categoria ha sido editada correctamente');
            return $this->redirectToRoute('backend_categoria_index');
        }

        return $this->render('backend/Categoria/editarCategoria.html.twig', [
            'categoria' => $categoria,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/eliminar/{id}", name="backend_categoria_eliminar", methods={"DELETE", "GET", "POST"})
     */
    public function eliminarCategoria(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoria = $entityManager->getRepository(Categoria::class)->findOneById($id);
        if(!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada.');
        }
        if ($this->isCsrfTokenValid('delete'.$categoria->getId(), $request->request->get('_token'))) {
            if(count($categoria->getArticulos()) > 0) {
                $this->addFlash('Error', 'No se puede eliminar la categoria porque contiene articulos');
            }else {
                $entityManager->remove($categoria);
                $entityManager->flush();
                $this->addFlash('Eliminado', 'La categoria ha sido eliminada');
            }
        }else{
            $this->addFlash('Error', 'Error al eliminar la categoria');
        }

        return $this->redirectToRoute('backend_categoria_index');
    }
}
