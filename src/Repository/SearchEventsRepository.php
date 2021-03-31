<?php

namespace App\Repository;

use App\Entity\SearchEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchEvents[]    findAll()
 * @method SearchEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchEventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchEvents::class);
    }

    // /**
    //  * @return SearchEvents[] Returns an array of SearchEvents objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchEvents
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
