<?php

namespace App\Repository;

use App\Entity\HistorialBusqueda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistorialBusqueda>
 *
 * @method HistorialBusqueda|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistorialBusqueda|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistorialBusqueda[]    findAll()
 * @method HistorialBusqueda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistorialBusquedaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistorialBusqueda::class);
    }

    public function add(HistorialBusqueda $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistorialBusqueda $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return HistorialBusqueda[] Returns an array of HistorialBusqueda objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistorialBusqueda
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
