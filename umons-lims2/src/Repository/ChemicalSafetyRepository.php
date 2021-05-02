<?php

namespace App\Repository;

use App\Entity\ChemicalSafety;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChemicalSafety|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChemicalSafety|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChemicalSafety[]    findAll()
 * @method ChemicalSafety[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChemicalSafetyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChemicalSafety::class);
    }

    // /**
    //  * @return ChemicalSafety[] Returns an array of ChemicalSafety objects
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
    public function findOneBySomeField($value): ?ChemicalSafety
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
