<?php

namespace App\Controller\Backend;

use App\Entity\Almacen;
use App\Entity\Articulo;
use App\Entity\ArticuloTalla;
use App\Form\Backend\ArticuloTalla\AsignarTallaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticuloTallaRepository;
use App\Repository\TallasRepository;

/**
 * @Route("/backend/articulo-talla")
 */
class ArticuloTallaController extends AbstractController
{
    /**
     * @Route("/asignar/{articulo_id}", name="backend_articulo_talla_asignar", methods={"GET", "POST"})
     */
    public function asignarTallas(Request $request, EntityManagerInterface $entityManager, ArticuloTallaRepository $articuloTallaRepository, TallasRepository $tallas, $articulo_id): Response
    {

        $articulo = $entityManager->getRepository(Articulo::class)->findOneBy(['id' => $articulo_id]);
        if(!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado');
        }
        $tallasAsignadas = $articuloTallaRepository->getTallasAsignadas($articulo->getId());
        $tallasNoAsignadas = $tallas->getTallasNoAsignadas(!empty($tallasAsignadas) ? $tallasAsignadas : 0, $articulo->getId()); 
        if($request->isMethod('POST') && $request->isXmlHttpRequest()){
            $almacen = $entityManager->getRepository(Almacen::class)->findOneBy(['id' => 1]);
            $talla = $tallas->findOneBy(['id' => $request->get('talla')]);
            $articuloTalla = $articuloTallaRepository->findOneBy(['talla' => $talla->getId(), 'articulo' => $articulo->getId()]);
            if(!$articuloTalla){
                $articuloTalla = new ArticuloTalla();
                $articuloTalla->setArticulo($articulo);
                $articuloTalla->setAlmacen($almacen);
                $articuloTalla->setTalla($talla);
                $request->get('asignar') == 'true' ? $articuloTalla->setActiva(true) : $articuloTalla->setActiva(false);
                $entityManager->persist($articuloTalla);
            }else{
                $request->get('asignar') == 'true' ? $articuloTalla->setActiva(true) : $articuloTalla->setActiva(false);
            }
            $entityManager->flush();
            $tallasAsignadas = $articuloTallaRepository->getTallasAsignadas($articulo->getId());
            $tallasNoAsignadas = $tallas->getTallasNoAsignadas(!empty($tallasAsignadas) ? $tallasAsignadas : 0, $articulo->getId());
            return new Response(json_encode([
                'type' => 'success',
                'message' => 'Talla asignada',
                'content' => $this->renderView('backend/ArticuloTalla/AsignarTallasLoad.html.twig', [
                    'articulo' => $articulo,
                    'tallasAsignadas' => $tallasAsignadas,
                    'tallasNoAsignadas' => $tallasNoAsignadas
                ])
                ]
            ));
        }
        return $this->render('backend/ArticuloTalla/AsignarTallas.html.twig', [
            'articulo' => $articulo,
            'tallasAsignadas' => $tallasAsignadas,
            'tallasNoAsignadas' => $tallasNoAsignadas
        ]);
    }

    /**
     * funcion para descontar existencias de articulos
     */
    public static function descontarExistencias(EntityManagerInterface $entityManager, $documento)
    {
        if($documento->getFinalizado() != true){
            foreach($documento->getRegistros() as $registro){
                if($registro->getArticulo()->getSobrePedido() == true){
                    $registro->getArticuloTalla()->setCantidadPedida($registro->getArticuloTalla()->getCantidadPedida() + $registro->getCantidad());
                }else{
                    $registro->getArticuloTalla()->setExistencia($registro->getArticuloTalla()->getExistencia() - $registro->getCantidad());
                    $registro->getArticuloTalla()->setCantidadApartada($registro->getArticuloTalla()->getCantidadApartada() + $registro->getCantidad());
                }
                $entityManager->flush();
            }
        }
    }
}
