<?php

namespace App\Repository;

use App\Entity\DocumentoPago;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentoPago|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentoPago|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentoPago[]    findAll()
 * @method DocumentoPago[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentoPagoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentoPago::class);
    }

    // /**
    //  * @return DocumentoPago[] Returns an array of DocumentoPago objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentoPago
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
