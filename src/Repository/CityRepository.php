<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
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
