<?php

namespace App\Entity;

use App\Repository\DailyAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailyAnswerRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_ANSWERED_ON', fields: ['user', 'answeredOn'])]
class DailyAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\ManyToOne(targetEntity: QuestionChoice::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionChoice $choice = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $answeredOn = null;

    /**
     * A daily answer is only ever recorded once the player found the correct
     * choice: wrong attempts don't lock the day, so every row here is a win.
     */
    public function __construct(User $user, Question $question, QuestionChoice $choice, \DateTimeImmutable $answeredOn)
    {
        $this->user = $user;
        $this->question = $question;
        $this->choice = $choice;
        $this->answeredOn = $answeredOn;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function getChoice(): ?QuestionChoice
    {
        return $this->choice;
    }

    public function getAnsweredOn(): ?\DateTimeImmutable
    {
        return $this->answeredOn;
    }
}
