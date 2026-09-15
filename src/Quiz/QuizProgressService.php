<?php

namespace App\Quiz;

use App\Entity\DailyAnswer;
use App\Entity\Question;
use App\Entity\QuestionChoice;
use App\Entity\User;
use App\Entity\UserQuizProgress;
use App\Repository\DailyAnswerRepository;
use App\Repository\UserQuizProgressRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Owns the cat's mood, the answer streak and the lifetime score: the rules
 * that turn "did the player answer today's question, and correctly?" into
 * game state.
 */
final class QuizProgressService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserQuizProgressRepository $progressRepository,
        private readonly DailyAnswerRepository $answerRepository,
    ) {
    }

    public function getOrCreateProgress(User $user): UserQuizProgress
    {
        $progress = $this->progressRepository->findOneForUser($user);
        if (null === $progress) {
            $progress = new UserQuizProgress($user);
            $this->entityManager->persist($progress);
            $this->entityManager->flush();
        }

        return $progress;
    }

    /**
     * Applies the mood/streak penalty for every full day that went by
     * without the player answering, since the last time this ran.
     */
    public function catchUpMissedDays(UserQuizProgress $progress, \DateTimeImmutable $today): void
    {
        $lastProcessed = $progress->getLastProcessedDate();
        if (null === $lastProcessed) {
            return;
        }

        $missedDays = $lastProcessed->diff($today)->days - 1;
        if ($missedDays > 0) {
            $progress->recordMissedDays($missedDays);
            $progress->resetStreak();
            $progress->setLastProcessedDate($today->modify('-1 day'));
            $this->entityManager->flush();
        }
    }

    public function findAnswerForDate(User $user, \DateTimeImmutable $date): ?DailyAnswer
    {
        return $this->answerRepository->findOneForUserAndDate($user, $date);
    }

    /**
     * The player can retry the same day's question as many times as needed:
     * only a correct choice closes out the day, so a wrong guess here is a
     * no-op that lets them try again immediately.
     *
     * @return DailyAnswer|null the recorded answer, or null when the choice was wrong
     *
     * @throws \DomainException if the player already answered today's question correctly
     */
    public function recordAnswer(User $user, Question $question, QuestionChoice $choice, \DateTimeImmutable $today): ?DailyAnswer
    {
        $progress = $this->getOrCreateProgress($user);
        $this->catchUpMissedDays($progress, $today);

        if (null !== $this->findAnswerForDate($user, $today)) {
            throw new \DomainException('learn.error.already_answered');
        }

        if (!$choice->isCorrect()) {
            return null;
        }

        $progress->recordCorrectAnswer();
        $progress->incrementStreak();
        $progress->incrementTotalCorrectAnswers();
        $progress->setLastProcessedDate($today);

        $answer = new DailyAnswer($user, $question, $choice, $today);
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return $answer;
    }
}
