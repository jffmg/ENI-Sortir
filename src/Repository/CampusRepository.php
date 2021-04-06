<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function findCampusByUser($userId)
    {
        return $this->createQueryBuilder('c')
            ->join('c.participants', 'p', 'WITH', 'p.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByKeyWord($keywordsArray)
    {
        $qb = $this->createQueryBuilder('c');
        $i = 0;
        foreach ($keywordsArray as $keyword) {
            $keyword = strtolower($keyword);
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('c.name', ":keyword" . $i)
            ));
            $qb->setParameter(":keyword" . $i, "%" . $keyword . "%");
            $i++;
        }
        $result = $qb->getQuery()->getResult();
        return $result;
    }

}
