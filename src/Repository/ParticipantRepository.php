<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements UserLoaderInterface
{

    public function loadUserByUsername(string $usernameOrEmail)
    {

        return $this->createQueryBuilder('p')
            ->where('p.userName = :username OR p.mail = :mail')
            ->setParameter('username', $usernameOrEmail)
            ->setParameter('mail', $usernameOrEmail)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }
}
