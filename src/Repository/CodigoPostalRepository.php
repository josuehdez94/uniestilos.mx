<?php

namespace App\Repository;

use App\Entity\CodigoPostal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CodigoPostal|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodigoPostal|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodigoPostal[]    findAll()
 * @method CodigoPostal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodigoPostalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodigoPostal::class);
    }

    // /**
    //  * @return CodigoPostal[] Returns an array of CodigoPostal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CodigoPostal
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
