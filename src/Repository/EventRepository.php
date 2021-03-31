<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\SearchEvents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

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
        if ($campus) {
            $qb->andWhere("e.campusOrganizer = :campus");
            $qb->setParameter("campus", $campus);
        }



        $keywords = $searchEvents->getKeywords();
        if ($keywords) {
            $keywords = explode(" ", trim($keywords));

            dump($keywords);

            $i = 0;
            foreach ($keywords as $keyword) {
                $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like('e.name', ":keyword".$i)
                ));
                $qb->setParameter(":keyword".$i, "%" . $keyword . "%");
                $i++;
            }

        }

        dump($qb->getDQL());

            // Todo requetes sur les dates

            $userIsOrganizer = $searchEvents->isUserIsOrganizer();
            if ($userIsOrganizer) {
                $qb->andWhere("e.organizer = :user");
                $qb->setParameter("user", $user);
            }

            // TODO tester les requetes quand on aura des fausses données pour les événements
            $filterEventsUserIsRegistered = $searchEvents->isUserIsRegistered();
            if (!$filterEventsUserIsRegistered) {
                // TODO enlever les evenements où l'utilisateur est inscrit
            }

            $filterEventsUserIsNotRegistered = $searchEvents->isUserIsNotRegistered();
            if (!$filterEventsUserIsNotRegistered) {
                // TODO enlever les evenements où l'utilisateur n'est pas inscrit
            }

            $query = $qb->getQuery();
            $result = $query->getResult();
            return $result;


    }
}
