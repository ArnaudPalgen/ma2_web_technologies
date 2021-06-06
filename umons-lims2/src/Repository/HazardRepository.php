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



    /**
      * @return Hazard[] Returns an array of Hazard objects
      */
    public function findByIdOrCreate($hrds) {

        $codes = array_map(fn($hrd) => $hrd['code'], $hrds);
        $hs =  $this->findBy(['code' => $codes]);

        $existing_hazard_codes = array_map(function ($e) {
            return $e->getId();
        }, $hs);

        $non_existing_hazard_codes = array_values(array_diff($ids, $existing_hazard_codes));

        $em = $this->getEntityManager();
        foreach ($non_existing_hazard_codes as $hazard_id) {
            $hazard = new Hazard();
            $hazard
                ->setCode("coucou")
                ->setLabel("coucou label");

            $hs[]= $hazard;
            $em->persist($hazard);

        }
        $em->flush();

        dd($hs);
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
