<?php

namespace App\Repository;

use App\Entity\Documento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Documento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documento[]    findAll()
 * @method Documento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documento::class);
    }

    // /**
    //  * @return Documento[] Returns an array of Documento objects
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
    public function findOneBySomeField($value): ?Documento
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getLastDocumentoId()
    {
        return $this->createQueryBuilder('d')
            ->select('MAX (d.id)')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getLastDocumentoByUser($cliente)
    {
        return $this->createQueryBuilder('d')
            ->select('MAX (d.id)')
            ->andWhere('d.cliente = :cliente')
            ->andWhere('d.tipo = :tipo')
            ->andWhere('d.finalizado != true')
            ->setParameter('cliente', $cliente)
            ->setParameter('tipo', 'R')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getLastDocumentoByUserAnon($cliente)
    {
        return $this->createQueryBuilder('d')
            ->select('MAX (d.id)')
            ->andWhere('d.userCookie = :cliente')
            ->andWhere('d.tipo = C')
            ->setParameter('cliente', $cliente)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getTotalArticulosByDocumento($documento){
        return $this->createQueryBuilder('d')
            ->select('SUM (dg.cantidad) as cantidad')
            ->innerJoin('\App\Entity\DocumentoRegistro', 'dg', 'WITH', 'd.id = dg.Documento')
            ->andWhere('d.id = :documento')
            ->setParameter('documento', $documento)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
