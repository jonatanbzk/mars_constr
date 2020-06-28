<?php

namespace App\Repository;

use App\Entity\ConstructionSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConstructionSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstructionSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstructionSite[]    findAll()
 * @method ConstructionSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstructionSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructionSite::class);
    }

    /**
     * @return Query
     */
    public function findAllQuery(): Query
    {
        $query = $this->createQueryBuilder('construction')
            ->orderBy('construction.name');
        return $query->getQuery();
    }

    // /**
    //  * @return ConstructionSite[] Returns an array of ConstructionSite objects
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
    public function findOneBySomeField($value): ?ConstructionSite
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
