<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Usage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * // * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findIncompatibilities($location, $hazardCodes)
    {


        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('id', 'id')
            ->addScalarResult('name', 'name')
            ->addScalarResult('ncas', 'ncas')
            ->addScalarResult('size', 'size')
            ->addScalarResult('concentration', 'concentration');

        $sql = "
            SELECT p.id,
                   name,
                   ncas,
                   size,
                   concentration
            FROM   product p
                   INNER JOIN (SELECT u1.product_id,
                                      u1.action,
                                      u2.date,
                                      u1.user_id
                               FROM   `usage` u1
                                      JOIN (SELECT product_id,
                                                   Max(date) date
                                            FROM   `usage`
                                            GROUP  BY product_id) u2
                                        ON u1.date = u2.date
                                           AND u1.product_id = u2.product_id) u
                           ON p.id = u.product_id
                              AND action != 4

                   INNER JOIN hazard_product hp
                           ON hp.product_id = p.id
            WHERE 
                  p.location_id = :location
                  and
                  EXISTS(
                      SELECT *
                      FROM   hazard_hazard incs
                      WHERE  incs.hazard_target = hp.hazard_id && incs.hazard_source IN (:pHazrads )
                      )  
        ";

        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('pHazrads', $hazardCodes)
            ->setParameter('location', $location);


        $res = $query->getResult();

        if (count($res) > 0) {
            return $res;
        }
        return null;


    }


    public function getProductList()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('id', 'id')
            ->addScalarResult('name', 'name')
            ->addScalarResult('ncas', 'ncas')
            ->addScalarResult('size', 'size')
            ->addScalarResult('concentration', 'concentration')
            ->addScalarResult('location', 'location')
            ->addScalarResult('user', 'user')
            ->addScalarResult('action', 'action');
        $sql = "
                SELECT p.id, name, ncas, size, concentration,concat(location.shelf, ' ', location.level) location, concat( user.first_name, ' ', user.last_name) user, action
                FROM product p
                         INNER JOIN (SELECT u1.product_id, u1.action, u2.date , u1.user_id
                                     from `usage` u1
                                            join (
                                         SELECT product_id, max(date) date
                                         FROM `usage`
                                         GROUP BY product_id
                                     ) u2 on u1.date = u2.date and u1.product_id = u2.product_id
                                     /* GROUP BY product_id*/) u ON p.id = u.product_id AND action != 4
                         left join user  on user.id = u.user_id
                         left join location  on location.id = p.location_id;
        ";
        $query = $this->getEntityManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter('action', Usage::ACTION_TAKE);


        return $query->getResult();
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
