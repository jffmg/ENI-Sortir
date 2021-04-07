<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\SearchEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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
        $qb->join('e.state', 'state', 'WITH', "state.shortLabel != 'AH'");
        $qb->andWhere($qb->expr()->orX(
            "state.shortLabel != 'EC'",
            "e.organizer = :user0"
        ));
        $qb->setParameter("user0", $user);
        $qb->addSelect('state');

        $campus = $searchEvents->getCampus();
        if ($campus)
        {
            $qb->andWhere("e.campusOrganizer = :campus");
            $qb->setParameter("campus", $campus);
        }



        $keywords = $searchEvents->getKeywords();
        if ($keywords) {
            $keywords = explode(" ", trim($keywords));

        //dump($qb->getDQL());

            $i = 0;
            foreach ($keywords as $keyword) {
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('e.name', ":keyword".$i)
                ));
                $qb->setParameter(":keyword".$i, "%" . $keyword . "%");
                $i++;
            }

        }

        $startDate = $searchEvents->getStartDate();
        $endDate = $searchEvents->getEndDate();
        $qb->andWhere("e.dateTimeStart > :startDate");
        $qb->andWhere("e.dateTimeStart < :endDate");
        $qb->setParameter("startDate", $startDate);
        $qb->setParameter("endDate", $endDate);

        // If checked => show only the events when user is the organizer, if not checked show all
        $userIsOrganizer = $searchEvents->isUserIsOrganizer();
        if ($userIsOrganizer)
        {
            $qb->andWhere("e.organizer = :user");
            $qb->setParameter("user", $user);
        }

        $isRegistered = $searchEvents->isUserIsRegistered();
        $isNotRegistered = $searchEvents->isUserIsNotRegistered();

        if ($isRegistered && !$isNotRegistered) {
            $qb->join('e.participants', 'ep', 'WITH', 'ep.id = :userId1');
            $qb->setParameter("userId1", $user);
            $qb->addSelect('ep');
        }

        if (!$isRegistered && $isNotRegistered) {

            $qb2 = $this->createQueryBuilder('e2');
            $qb2->join('e2.participants', 'ep2', 'WITH', 'ep2.id = :userId2');
            $qb2->andWhere('e.id = e2.id');

            $qb->andWhere($qb->expr()->not($qb->expr()->exists($qb2)));
            $qb->setParameter("userId2", $user);
        }

        $endedEvents = $searchEvents->isEndedEvents();
        if ($endedEvents)
        {
            $qb->join('e.state','s', 'WITH', "s.shortLabel = 'AT'");
            $qb->addSelect('s');
        }

        $qb->orderBy("e.dateTimeStart");

        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

}
