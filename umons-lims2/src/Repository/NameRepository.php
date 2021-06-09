<?php

namespace App\Repository;

use App\Entity\Name;
use App\Entity\Usage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Name|null find($id, $lockMode = null, $lockVersion = null)
 * @method Name|null findOneBy(array $criteria, array $orderBy = null)
 * @method Name[]    findAll()
 * @method Name[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Name::class);
    }


    public function searchByNcas(string $ncas) {

        if($ncas == null) {
            return [];
        }
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('id', 'id')
            ->addScalarResult('name', 'name')
            ->addScalarResult('ncas', 'ncas')
            ->addScalarResult('score', 'score');

        $sql = "
               SELECT 
                      *,
                      MATCH(ncas) AGAINST(:ncas) score 
               FROM name
               ORDER BY score DESC
        ";
        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('ncas', $ncas);
        return $query->getResult();
    }

    // /**
    //  * @return Name[] Returns an array of Name objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Name
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
