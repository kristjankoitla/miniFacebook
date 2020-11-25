<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findByUser(UserInterface $user) {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.text')
            ->addSelect('u.id AS user_id')
            ->addSelect('p.id AS post_id')
            ->orderBy('p.id', 'DESC')
            ->innerJoin('p.user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getUsername());

        return $qb->getQuery()->getResult();
    }

    public function findFriendsPosts(UserInterface $user) {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('App\Entity\Friendship', 'f', 'WITH', 'p.user = f.receiver OR p.user = f.initiator')
            ->where('f.initiator = :user')
            ->orWhere('f.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->getQuery()->getResult();
    }

}
