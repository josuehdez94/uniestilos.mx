<?php

namespace App\Repository;

use App\Entity\ArticuloDescripcion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticuloDescripcion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticuloDescripcion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticuloDescripcion[]    findAll()
 * @method ArticuloDescripcion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticuloDescripcionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticuloDescripcion::class);
    }

    // /**
    //  * @return ArticuloDescripcion[] Returns an array of ArticuloDescripcion objects
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
    public function findOneBySomeField($value): ?ArticuloDescripcion
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
