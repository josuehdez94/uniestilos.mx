<?php

namespace App\Repository;

use App\Entity\RoleSistema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoleSistema|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoleSistema|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoleSistema[]    findAll()
 * @method RoleSistema[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleSistemaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleSistema::class);
    }

    // /**
    //  * @return RoleSistema[] Returns an array of RoleSistema objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoleSistema
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
