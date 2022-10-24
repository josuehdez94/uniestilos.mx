<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#formularios
use \App\Form\Backend\ArticuloDescripcion\ArticuloDescripcionType;

/**
 * @Route("/backend/articulos/descripcion")
 */
class ArticuloDescripcionController extends AbstractController
{
    /**
     * @Route("/{articulo_id}", name="backend_articulo_descripcion_editar", methods={"GET", "POST"})
     */
    public function descripcionArticulo(Request $request, $articulo_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneBy(['id' => $articulo_id]);
        if(!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado');
        }
        if(empty($articulo->getArticuloDescripcion())){
            $descripcion = new \App\Entity\ArticuloDescripcion();
        }else{
            $descripcion = $entityManager->getRepository(\App\Entity\ArticuloDescripcion::class)->findOneBy(['id' => $articulo->getArticuloDescripcion()->getId()]);
        }
        $form = $this->createForm(ArticuloDescripcionType::class, $descripcion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(empty($articulo->getArticuloDescripcion())){
                $descripcion->setArticulo($articulo);
                $descripcion->setUsuarioCreador($this->getUser());
                $entityManager->persist($descripcion);
            }else{
                $descripcion->setUsuarioEditor($this->getUser());
            }
            $entityManager->flush();
            $this->addFlash('Editado', 'La descripción del articulo ha sido editada correctamente');
            return $this->redirectToRoute('backend_articulo_descripcion_editar', [
                'articulo_id' => $articulo->getId()
            ]);
        }

        return $this->render('backend/ArticuloDescripcion/descripcionArticulo.html.twig', [
            'articulo' => $articulo,
            'submenu' => 'descripcion',
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/detalles/{articulo_id}", name="backend_articulo_descripcion_detalles", methods={"GET", "POST"})
     */
    public function descripcionArticuloDetalles($articulo_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneBy(['id' => $articulo_id]);
        if(!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado');
        }
        return $this->render('backend/ArticuloDescripcion/descripcionArticuloDetalles.html.twig', [
            'articulo' => $articulo,
        ]);
    }
}
