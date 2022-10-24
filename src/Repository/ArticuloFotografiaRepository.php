<?php

namespace App\Repository;

use App\Entity\ArticuloFotografia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticuloFotografia|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticuloFotografia|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticuloFotografia[]    findAll()
 * @method ArticuloFotografia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticuloFotografiaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticuloFotografia::class);
    }

    // /**
    //  * @return ArticuloFotografia[] Returns an array of ArticuloFotografia objects
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
    public function findOneBySomeField($value): ?ArticuloFotografia
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
