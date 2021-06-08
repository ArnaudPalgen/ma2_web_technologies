<?php

namespace App\Repository;

use App\Entity\Hazard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hazard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hazard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hazard[]    findAll()
 * @method Hazard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HazardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hazard::class);
    }



    // /**
    //  * @return Hazard[] Returns an array of Hazard objects
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
    public function findOneBySomeField($value): ?Hazard
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
