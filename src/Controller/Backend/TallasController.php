<?php

namespace App\Controller\Backend;

use App\Entity\Tallas;
use App\Form\Backend\Tallas\TallaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TallasRepository;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/backend/talla")
 */
class TallasController extends AbstractController
{
    /**
     * @Route("", name="backend_talla_index", methods={"GET"})
     */
    public function indexTallas(TallasRepository $tallasRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $tallas = $paginator->paginate(
            $tallasRepository->findAll(),
            $request->query->getInt('pagina', 1),
            50
        );
        return $this->render('backend/Tallas/index.html.twig', [
            'tallas' => $tallas,
        ]);
    }

    /**
     * @Route("/nueva", name="backend_talla_nueva", methods={"GET", "POST"})
     */
    public function nuevaTalla(EntityManagerInterface $entityManager, Request $request): Response
    {
        $talla = new Tallas();
        $form = $this->createForm(TallaType::class, $talla);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $nombre = $form->get('nombre')->getData();
            $talla->setNombre(strtoupper($nombre));
            $entityManager->persist($talla);
            $entityManager->flush();
            $this->addFlash('Creado', 'Talla creada correctamente');
            return $this->redirectToRoute('backend_talla_index');
        }
        return $this->render('backend/Tallas/nueva.html.twig', [
            'tallas' => $talla,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/editar/{id}", name="backend_talla_editar", methods={"GET", "POST"})
     */
    public function editarTalla(EntityManagerInterface $entityManager, Request $request, $id): Response
    {
        $talla = $entityManager->getRepository(Tallas::class)->findOneBy(['id' => $id]);
        if(!$talla){
            throw $this->createNotFoundException('Talla no encontrada');
        }
        $form = $this->createForm(TallaType::class, $talla);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $nombre = $form->get('nombre')->getData();
            $talla->setNombre(strtoupper($nombre));
            $entityManager->persist($talla);
            $entityManager->flush();
            $this->addFlash('Editado', 'Talla editada correctamente');
            return $this->redirectToRoute('backend_talla_index');
        }
        return $this->render('backend/Tallas/editar.html.twig', [
            'talla' => $talla,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/eliminar", name="backend_talla_eliminar", methods={"POST", "DELETE"})
     */
    public function eliminarTalla(EntityManagerInterface $entityManager, Request $request): Response
    {
        if(null === $request->get('id')){
            throw $this->createNotFoundException('No se recibio id de Talla');
        }
        $talla = $entityManager->getRepository(Tallas::class)->findOneBy(['id' => $request->get('id')]);
        if(!$talla){
            throw $this->createNotFoundException('Talla no encontrada');
        }
        if(count($talla->getTallas()) > 0){
            $talla->setDesactivada(true);
            $this->addFlash('Eliminado', 'Talla fue desactivada con exito');
        }else{
            $this->addFlash('Eliminado', 'Talla eliminada correctamente');
            $entityManager->remove($talla);
        }
        $entityManager->flush();
        return $this->redirectToRoute('backend_talla_index');

    }
    
}
