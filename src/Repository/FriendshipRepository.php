<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function findFriendshipByUsers(UserInterface $user1, UserInterface $user2)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->innerJoin('f.initiator', 'i')
            ->innerJoin('f.receiver', 'r')
            ->where('i.id = :idOne AND r.id = :idTwo')
            ->orWhere('i.id = :idTwo AND r.id = :idOne')
            ->setParameter('idOne', $user1->getUsername())
            ->setParameter('idTwo', $user2->getUsername());

        return $qb->getQuery()->getResult();
    }
}
