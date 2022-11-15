<?php

namespace App\Repository;

use App\Entity\Articulo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Articulo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articulo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articulo[]    findAll()
 * @method Articulo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticuloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articulo::class);
    }

    // /**
    //  * @return Articulo[] Returns an array of Articulo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articulo
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /** 
     * funcion para busqueda avanzada de articulos
    */
    public function busquedaAvanzadaBack($termino)
    {
        $busqueda = explode(' ', $termino);
        $terminos = [];
        for ($i = 0; $i < count($busqueda); $i++) {
            $terminos [] = '*' . preg_replace('([^A-Za-z0-9/_Ññ!¬&%$#])', "", $busqueda[$i]);
            $terminos [] = '+' . preg_replace('([^A-Za-z0-9/_Ññ!¬&%$#])', "", $busqueda[$i]);
        }
        $busquedaFinal = implode(' ', $terminos);
        if (!is_null($termino)) {
            # se cambia a busquedas boleanas match para hacer mas exacta la busqueda
            # el simbolo > 0.8 y > 10 siento que no mejora en nada la busqueda y no encontre cuales son los valores validos
            # ya que a veces funciona con decimales pero tambien funciona mejor con enteros
            $match = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andwhere("MATCH (articulo.sku, articulo.descripcion) AGAINST (:string_bool IN BOOLEAN MODE) > 0.8")
                    ->setParameter('string_bool', $busquedaFinal)
                    ->groupBy('articulo.id')
            ;
            # validacion para ver si se encontraron resultados con MATCH
            if ($match->getQuery()->getResult()) {
                return $match;
            }
            # consulta con like
            $like = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("articulo.id LIKE :str OR articulo.sku LIKE :str OR articulo.descripcion LIKE :str")
                    ->setParameter('str', "%" . $termino . "%")
                    ->groupBy('articulo.id')
            ;
            if ($like->getQuery()->getResult()) {
                return $like;
            }

            # se revisa que solo sean letras ya que soundex funciona muy lento con numeros
            if (!preg_match('/[A-Za-z].*[A-Za-z]/', $termino)) {
                return [];
            }

            # por ultimo sino se encontraron resultados con match y like se verifica si alguna
            # descripcion suena parecida como ultimo recurso y mostrar algunas recomendaciones
            # al igual funciona si es que escribieron mal la palabra
            # presenta fallas cuando escriben mal un codigo ya que se satura la memoria de tantas comparaciones
            $soundex = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("
                        SOUNDEX(articulo.descripcion) like CONCAT(SOUNDEX(:string),'%')
                    ")
                    ->setParameter('string', $termino)
                    ->groupBy('articulo.id')
            ;
            if ($soundex->getQuery()->getResult()) {
                return $soundex;
            }
        }
        return [];
    }

    ######################################################################################################################################################################
    ################################# FUNCIONES FRONT ####################################################################################################################


    /** 
     * funcion para busqueda avanzada de articulos
    */
    public function busquedaAvanzadaFront($termino)
    {
        $busqueda = explode(' ', $termino);
        $terminos = [];
        for ($i = 0; $i < count($busqueda); $i++) {
            $terminos [] = '*' . preg_replace('([^A-Za-z0-9/_Ññ!¬&%$#])', "", $busqueda[$i]);
            $terminos [] = '+' . preg_replace('([^A-Za-z0-9/_Ññ!¬&%$#])', "", $busqueda[$i]);
        }
        $busquedaFinal = implode(' ', $terminos);
        if (!is_null($termino)) {
            # se cambia a busquedas boleanas match para hacer mas exacta la busqueda
            # el simbolo > 0.8 y > 10 siento que no mejora en nada la busqueda y no encontre cuales son los valores validos
            # ya que a veces funciona con decimales pero tambien funciona mejor con enteros
            $match = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andwhere("MATCH (articulo.sku, articulo.descripcion) AGAINST (:string_bool IN BOOLEAN MODE) > 0.8")
                    ->setParameter('string_bool', $busquedaFinal)
                    ->groupBy('articulo.id')
            ;
            # validacion para ver si se encontraron resultados con MATCH
            if ($match->getQuery()->getResult()) {
                $articulos = $this->createQueryBuilder('articulo')
                    ->select('articulo.id')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andwhere("MATCH (articulo.sku, articulo.descripcion) AGAINST (:string_bool IN BOOLEAN MODE) > 0.8")
                    ->setParameter('string_bool', $busquedaFinal)
                    ->groupBy('articulo.id')
                    ->getQuery()->getResult()
                ;
                return [ 'busqueda' => $match, 'articulos' => $articulos ];
            }
            # consulta con like
            $like = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("articulo.id LIKE :str OR articulo.sku LIKE :str OR articulo.descripcion LIKE :str")
                    ->setParameter('str', "%" . $termino . "%")
                    ->groupBy('articulo.id')
            ;
            if ($like->getQuery()->getResult()) {
                $articulos = $this->createQueryBuilder('articulo')
                    ->select('articulo.id')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("articulo.id LIKE :str OR articulo.sku LIKE :str OR articulo.descripcion LIKE :str")
                    ->setParameter('str', "%" . $termino . "%")
                    ->groupBy('articulo.id')
                    ->getQuery()->getResult()
                ;
                return [ 'busqueda' => $like, 'articulos' => $articulos ];
            }

            # se revisa que solo sean letras ya que soundex funciona muy lento con numeros
            if (!preg_match('/[A-Za-z].*[A-Za-z]/', $termino)) {
                return ['busqueda' => null, 'articulos' => 0];
            }

            # por ultimo sino se encontraron resultados con match y like se verifica si alguna
            # descripcion suena parecida como ultimo recurso y mostrar algunas recomendaciones
            # al igual funciona si es que escribieron mal la palabra
            # presenta fallas cuando escriben mal un codigo ya que se satura la memoria de tantas comparaciones
            $soundex = $this->createQueryBuilder('articulo')
                    ->addSelect('articuloFotografia')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("
                        SOUNDEX(articulo.descripcion) like CONCAT(SOUNDEX(:string),'%')
                    ")
                    ->setParameter('string', $termino)
                    ->groupBy('articulo.id')
            ;
            if ($soundex->getQuery()->getResult()) {
                $articulos = $this->createQueryBuilder('articulo')
                    ->select('articulo.id')
                    ->leftJoin('articulo.fotografiaPrincipal', 'articuloFotografia')
                    ->leftJoin('articulo.subcategoria', 'subcategoria')
                    ->where('articulo.activo = true')
                    ->andWhere("
                        SOUNDEX(articulo.descripcion) like CONCAT(SOUNDEX(:string),'%')
                    ")
                    ->setParameter('string', $termino)
                    ->groupBy('articulo.id')
                    ->getQuery()->getResult()
                ;
                return [ 'busqueda' => $soundex, 'articulos' => $articulos ];
            }
        }
        return ['busqueda' => null, 'articulos' => 0];
    }

    /**
     * funcion para obtener articulos por una categoria en especifico
     * @param categoria
     */
    public function getArticulosPorCategoria($categoria)
    {
        return $this->createQueryBuilder('articulo')
            ->innerJoin('articulo.subcategoria', 'subcategoria')
            ->innerJoin('subcategoria.categoria', 'categoria')
            ->andWhere('categoria.id = :val')
            ->andWhere('articulo.activo = true')
            ->setParameter('val', $categoria)
            ->orderBy('articulo.id', 'ASC')
        ;
    }


    /**
     * funcion para obtener articulos por una subcategoria en especifico
     * @param subcategoria
     */
    public function getArticulosPorSubcategoria($subcategoria)
    {
        return $this->createQueryBuilder('articulo')
            ->innerJoin('articulo.subcategoria', 'subcategoria')
            ->andWhere('subcategoria.id = :val')
            ->andWhere('articulo.activo = true')
            ->setParameter('val', $subcategoria)
            ->orderBy('articulo.id', 'ASC')
        ;
    }

    /**
     * funcion para obtener articulos por una categoria en especifico
     * @param articulos
     */
    public function getClasificacionesParaFiltros($articulos)
    {
        return $this->createQueryBuilder('articulo')
            ->select('articulo.clasificacion')
            ->andWhere('articulo.id IN (:articulos)')
            ->andWhere('articulo.activo = true')
            ->setParameter('articulos', $articulos)
            ->groupBy('articulo.clasificacion')
            ->getQuery()
            ->getResult()
        ;
    }
}
