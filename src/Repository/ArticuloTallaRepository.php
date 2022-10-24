<?php

namespace App\Repository;

use App\Entity\ArticuloTalla;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticuloTalla|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticuloTalla|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticuloTalla[]    findAll()
 * @method ArticuloTalla[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticuloTallaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticuloTalla::class);
    }

    // /**
    //  * @return ArticuloTalla[] Returns an array of ArticuloTalla objects
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
    public function findOneBySomeField($value): ?ArticuloTalla
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getTallasAsignadas($articulo)
    {
        return $this->createQueryBuilder('a')
            ->select('talla.id')
            ->innerJoin('a.talla', 'talla')
            ->andWhere('a.articulo = :articulo')
            ->andWhere('a.activa = true')
            ->setParameter('articulo', $articulo)
            ->getQuery()
            ->getResult()
        ;
    }

    
}
