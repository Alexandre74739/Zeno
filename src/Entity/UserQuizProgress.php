<?php

namespace App\Entity;

use App\Quiz\CatMood;
use App\Repository\UserQuizProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserQuizProgressRepository::class)]
class UserQuizProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?User $user = null;

    #[ORM\Column]
    private int $mood = CatMood::Neutral->value;

    /**
     * Correct answers banked toward leveling up the current mood tier; each
     * tier needs a growing number of them (see CatMood::progressThreshold()).
     */
    #[ORM\Column(options: ['default' => 0])]
    private int $moodProgress = 0;

    #[ORM\Column]
    private int $currentStreak = 0;

    #[ORM\Column]
    private int $totalCorrectAnswers = 0;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastProcessedDate = null;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getMood(): int
    {
        return $this->mood;
    }

    public function getMoodEnum(): CatMood
    {
        return CatMood::from($this->mood);
    }

    public function getMoodProgress(): int
    {
        return $this->moodProgress;
    }

    /**
     * Percentage (0-100) toward the next mood tier, for a progress bar. A cat
     * already at the top tier is always shown full: there is nothing left to
     * climb toward.
     */
    public function getMoodProgressPercent(): int
    {
        $threshold = $this->getMoodEnum()->progressThreshold();
        if (null === $threshold) {
            return 100;
        }

        return (int) min(100, round($this->moodProgress / $threshold * 100));
    }

    /**
     * Banks one correct answer toward the current mood tier's threshold,
     * leveling the mood up and resetting the bar once it is reached.
     */
    public function recordCorrectAnswer(): void
    {
        $threshold = $this->getMoodEnum()->progressThreshold();
        if (null === $threshold) {
            return;
        }

        ++$this->moodProgress;
        if ($this->moodProgress >= $threshold) {
            $this->mood = min(CatMood::Satisfied->value, $this->mood + 1);
            $this->moodProgress = 0;
        }
    }

    /**
     * Drops the mood by one tier per missed day (floored at Angry) and
     * restarts the progress bar for the tier landed on.
     */
    public function recordMissedDays(int $missedDays): void
    {
        $this->mood = max(CatMood::Angry->value, $this->mood - $missedDays);
        $this->moodProgress = 0;
    }

    public function getCurrentStreak(): int
    {
        return $this->currentStreak;
    }

    public function incrementStreak(): void
    {
        ++$this->currentStreak;
    }

    public function resetStreak(): void
    {
        $this->currentStreak = 0;
    }

    public function getTotalCorrectAnswers(): int
    {
        return $this->totalCorrectAnswers;
    }

    public function incrementTotalCorrectAnswers(): void
    {
        ++$this->totalCorrectAnswers;
    }

    public function getLastProcessedDate(): ?\DateTimeImmutable
    {
        return $this->lastProcessedDate;
    }

    public function setLastProcessedDate(\DateTimeImmutable $date): void
    {
        $this->lastProcessedDate = $date;
    }
}
