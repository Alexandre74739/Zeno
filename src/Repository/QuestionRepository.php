<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Deterministically picks the question of the day: every player sees the
     * same question on a given calendar day, and it rotates through the bank.
     */
    public function findForDate(\DateTimeImmutable $date): ?Question
    {
        $total = $this->count([]);
        if (0 === $total) {
            return null;
        }

        $dayNumber = intdiv((int) $date->format('U'), 86400);
        $offset = $dayNumber % $total;

        return $this->createQueryBuilder('q')
            ->orderBy('q.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
