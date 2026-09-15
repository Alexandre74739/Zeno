<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserQuizProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserQuizProgress>
 */
class UserQuizProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserQuizProgress::class);
    }

    public function findOneForUser(User $user): ?UserQuizProgress
    {
        return $this->findOneBy(['user' => $user]);
    }
}
