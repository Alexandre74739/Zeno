<?php

namespace App\Repository;

use App\Entity\DailyAnswer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DailyAnswer>
 */
class DailyAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyAnswer::class);
    }

    public function findOneForUserAndDate(User $user, \DateTimeImmutable $date): ?DailyAnswer
    {
        return $this->findOneBy(['user' => $user, 'answeredOn' => $date]);
    }
}
