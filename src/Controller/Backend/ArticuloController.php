<?php

namespace App\Controller\Backend;

use App\Entity\Articulo;
use App\Repository\ArticuloRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
#formularios
use App\Form\Backend\Articulo\ArticuloType;

/**
 * @Route("/backend/articulos")
 */
class ArticuloController extends AbstractController
{
    /**
     * @Route("", name="backend_articulo_index", methods={"GET"})
     */
    public function indexArticulo(ArticuloRepository $articuloRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $articulos = $paginator->paginate(
            $articuloRepository->findAll(),
            $request->query->getInt('pagina', 1),
            70
        );
        if($request->isXmlHttpRequest()){
            if(null !== $request->get('busqueda')){
                $articulos = $paginator->paginate(
                    $articuloRepository->busquedaAvanzadaBack($request->get('busqueda')),
                    $request->query->getInt('pagina', 1),
                    70
                );
                return new Response(
                    json_encode([
                        "type" => 'success',
                        'content' => $this->renderView('backend/Articulo/busquedaArticulo.html.twig', [
                                'articulos' => $articulos
                            ]),
                    ])
                );
            }  
        }
        if($request->isXmlHttpRequest()){
            return new Response(
                json_encode([
                    "type" => 'success',
                    'content' => $this->renderView('backend/Articulo/busquedaArticulo.html.twig', [
                            'articulos' => $articulos
                        ]),
                ])
            );
        }
        return $this->render('backend/Articulo/indexArticulo.html.twig', [
            'articulos' => $articulos,
        ]);
    }

    /**
     * @Route("/nuevo", name="backend_articulo_nuevo", methods={"GET","POST"})
     */
    public function nuevoArticulo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $articulo = new Articulo();
        $form = $this->createForm(ArticuloType::class, $articulo, ['categoria' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articulo->setUsuarioCreador($this->getUser());
            $entityManager->persist($articulo);
            $entityManager->flush();
            $this->addFlash('Creado', 'Articulo creado correctamente');
            return $this->redirectToRoute('backend_articulo_index');
        }

        return $this->render('backend/Articulo/nuevoArticulo.html.twig', [
            'articulo' => $articulo,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/detalles/{id}", name="backend_articulo_detalles", methods={"GET"})
     */
    public function detallesArticulo(EntityManagerInterface $entityManager, $id): Response
    {
        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneBy(['id' => $id]);
        if(!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado');
        }
        return $this->render('backend/Articulo/detallesArticulo.html.twig', [
            'articulo' => $articulo
        ]);
    }

    /**
     * @Route("/editar/generales/{id}", name="backend_articulo_editar", methods={"GET","POST"})
     */
    public function editarArticulo(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $articulo = $entityManager->getRepository(Articulo::class)->findOneBy(['id' => $id]);
        if(!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado');
        }
        $form = $this->createForm(ArticuloType::class, $articulo, ['categoria' => $articulo->getSubcategoria()->getCategoria()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articulo->setUsuarioEditor($this->getUser());
            $entityManager->flush();
            $this->addFlash('Editado', 'El articulo ha sido editado correctamente');
            return $this->redirectToRoute('backend_articulo_editar', [
                'id' => $articulo->getId()
            ]);
        }

        return $this->render('backend/Articulo/editarArticuloGenerales.html.twig', [
            'articulo' => $articulo,
            'submenu' => 'generales',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="articulo_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Articulo $articulo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$articulo->getId(), $request->request->get('_token'))) {
            $entityManager->remove($articulo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('articulo_index');
    }
}
