<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Articulo;
use App\Entity\Categoria;
use App\Entity\Subcategoria;
use App\Repository\ArticuloRepository;
//use Knp\Component\Pager\Pagination\PaginatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;


// SDK de Mercado Pago
//require 'C:/xampp/htdocs/MyProjects/miWeb/vendor/autoload.php';

// Agrega credenciales
//MercadoPago\SDK::setAccessToken('PROD_ACCESS_TOKEN');
//use MercadoPago\SDK;

//require_once '/var/www/tiendaonline/pruebaMercadoPago/vendor/autoload.php'; // You have to require the library from your Composer vendor folder


/**
 * @Route("/articulo")
 */
class ArticuloController extends AbstractController
{
    /**
     * @Route("/detalles/{urlAmigable}", name="frontend_articulo_detalles", methods={"GET"})
     */
    public function detallesArticulo($urlAmigable)
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $articulo = $entityManager->getRepository(Articulo::class)->findOneBy(['urlAmigable' => $urlAmigable]);
        if (!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado.');
        }
        #token MP
        $sdk = new \MercadoPago\SDK();
        $token = $sdk::setAccessToken('TEST-723413152291982-102921-c6c27b339ebb97114cb527fc63436a3d-264821330');
        #Crea un objeto de preferencia
        $preference = new \MercadoPago\Preference();
        #Crea un ítem en la preferencia
        $item = new \MercadoPago\Item();
        $item->title = $articulo->getDescripcion();
        $item->quantity = 1;
        $item->unit_price = $articulo->getPrecio1();
        $preference->items = array($item);
        $preference->save();
        return $this->render('Frontend/Articulo/detallesArticulo.html.twig', [
            'articulo' => $articulo,
            'preference' => $preference
        ]);
    }

    /**
     * @Route("/por-categoria/{urlAmigable}", name="frontend_articulo_por_categoria", methods={"GET"})
     */
    public function articulosPorCategoria(Request $request, PaginatorInterface $paginator, ArticuloRepository $articuloRepository, $urlAmigable, EntityManagerInterface $entityManager)
    {   
        $categoria = $entityManager->getRepository(Categoria::class)->findOneBy(['urlAmigable' => $urlAmigable]);
        if (!$categoria){
            throw $this->createNotFoundException('Categoria no encontrada.');
        }
        $articulos = $paginator->paginate(
            $articuloRepository->getArticulosPorCategoria($categoria->getId()),
            $request->query->getInt('pagina', 1),
            54
        );
        return $this->render('Frontend/Articulo/articulosPorCategoria.html.twig', [
            'articulos' => $articulos,
            'categoria' => $categoria
        ]);
    }

    /**
     * @Route("/por-subcategoria/{urlAmigable}", name="frontend_articulo_por_subcategoria", methods={"GET"})
     */
    public function articulosPorSubcategoria(Request $request, PaginatorInterface $paginator, ArticuloRepository $articuloRepository, $urlAmigable, EntityManagerInterface $entityManager)
    { 
        $subcategoria = $entityManager->getRepository(Subcategoria::class)->findOneBy(['urlAmigable' => $urlAmigable]);
        if (!$subcategoria){
            throw $this->createNotFoundException('Subcategoria no encontrada.');
        }
        $articulos = $paginator->paginate(
            $articuloRepository->getArticulosPorSubcategoria($subcategoria->getId()),
            $request->query->getInt('pagina', 1),
            54
        );
        return $this->render('Frontend/Articulo/articulosPorSubcategoria.html.twig', [
            'articulos' => $articulos,
            'subcategoria' => $subcategoria
        ]);
    }
}
