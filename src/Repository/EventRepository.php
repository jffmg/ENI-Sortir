<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\SearchEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function filterEvents(SearchEvents $searchEvents)
    {
        $qb = $this->createQueryBuilder('e');

        $campus = $searchEvents->getCampus();
        if ($campus)
        {
            $qb->andWhere("e.campusOrganizer = :campus");
            $qb->setParameter("campus", $campus);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}
