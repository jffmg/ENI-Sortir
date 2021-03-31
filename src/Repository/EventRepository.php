<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Participant;
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

    public function filterEvents(SearchEvents $searchEvents, Participant $user)
    {
        $qb = $this->createQueryBuilder('e');

        $campus = $searchEvents->getCampus();
        if ($campus)
        {
            $qb->andWhere("e.campusOrganizer = :campus");
            $qb->setParameter("campus", $campus);
        }

        // Todo régler le problème du passage d eparamètre à la requete et verifier si elle fonctionne
        /*$keywords = $searchEvents->getKeywords();
        if ($keywords)
        {
            $qb->expr()->in("e.name", array("?1"));
            $qb->setParameter(1, $keywords);
        }*/

        // Todo requetes sur les dates
        $startDate = $searchEvents->getStartDate();
        $endDate = $searchEvents->getEndDate();
        $qb->andWhere("e.dateTimeStart > :startDate");
        $qb->andWhere("e.dateTimeStart < :endDate");
        $qb->setParameter("startDate", $startDate);
        $qb->setParameter("endDate", $endDate);

        $userIsOrganizer = $searchEvents->isUserIsOrganizer();
        if ($userIsOrganizer)
        {
            $qb->andWhere("e.organizer = :user");
            $qb->setParameter("user", $user);
        }

        // TODO tester les requetes quand on aura des fausses données pour les événements
        $filterEventsUserIsRegistered = $searchEvents->isUserIsRegistered();
        if (!$filterEventsUserIsRegistered)
        {
            // TODO enlever les evenements où l'utilisateur est inscrit
        }

        $filterEventsUserIsNotRegistered = $searchEvents->isUserIsNotRegistered();
        if (!$filterEventsUserIsNotRegistered)
        {
            // TODO enlever les evenements où l'utilisateur n'est pas inscrit
        }

        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}
