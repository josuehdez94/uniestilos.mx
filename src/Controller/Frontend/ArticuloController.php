<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Articulo;
use App\Entity\Categoria;
use App\Entity\HistorialBusqueda;
use App\Entity\Subcategoria;
use App\Repository\ArticuloRepository;
use App\Repository\CategoriaRepository;
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
    public function detallesArticulo(EntityManagerInterface $entityManager, $urlAmigable)
    {   
        $articulo = $entityManager->getRepository(Articulo::class)->findOneBy(['urlAmigable' => $urlAmigable]);
        if (!$articulo){
            throw $this->createNotFoundException('Articulo no encontrado.');
        }
        if($articulo->getActivo() == false){
            throw $this->createNotFoundException('Articulo no disponible.'); 
        }
        return $this->render('Frontend/Articulo/detallesArticulo.html.twig', [
            'articulo' => $articulo,
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

    /**
     * @Route("/buscar/", name="frontend_articulo_buscar", methods={"GET"})
     */
    public function buscador(Request $request, PaginatorInterface $paginator, ArticuloRepository $articuloRepository, CategoriaRepository $categoriaRepository, EntityManagerInterface $entityManager)
    {   
        # se limpia el parametro que no traiga mas de 1 espacio en blanco entre cada palabra
        $q = $request->get('busqueda');
        # se eliminan espacios multiples entre palabras
        $q = preg_replace('/\s+/', ' ', $q);
        # se eliminan caracteres que no sean letras y numeros y un espacio al final para que respete espacios simples
        $q = preg_replace('([^A-Za-z0-9/_Ññ] )', "", $q);
        # se eliminan espacios al inicio o final
        $q = trim($q);
        $articulos = null;
        if (!empty($q)) {
            #generar historial de busqueda
            $historial = $entityManager->getRepository(HistorialBusqueda::class)->findOneBy(['termino' => $q]);
            if($historial){
                $historial->setNumeroBusquedas($historial->getNumeroBusquedas() + 1);
                $historial->setFechaHoraUltimaBusqueda(new \DateTime());
            }else{
                $historial = new HistorialBusqueda();
                $historial->setTermino($q);
                $historial->setNumeroBusquedas(1);
                $historial->setFechaHoraUltimaBusqueda(new \DateTime());
                $entityManager->persist($historial);
            }
            $entityManager->flush();
            $busqueda = $articuloRepository->busquedaAvanzadaFront($q);
            $articulos = $paginator->paginate(
                $busqueda['busqueda'],
                $request->query->getInt('pagina', 1),
                100
            );
            #filtros para busquedas
            $clasificaciones = $articuloRepository->getClasificacionesParaFiltros($busqueda['articulos']);
            $categorias = [];
            foreach($clasificaciones as $clasificacion){
                $categoria = $categoriaRepository->getCategoriasPorClasificacion($clasificacion['clasificacion']);
                $categorias = $categoria;
            }
            /* dump($categorias);
            exit(); */
            return $this->render('Frontend/Articulo/buscador.html.twig', [
                'articulos' => $articulos,
                'busqueda' => $q,
                'clasificaciones' => $clasificaciones,
                'categorias' => $categorias
            ]);
        }
        return $this->render('Frontend/Articulo/buscador.html.twig', [
            'articulos' => $articulos,
            'busqueda' => $q,
            'clasificaciones' => null,
            'categorias' => null
        ]);
        
    }
}
