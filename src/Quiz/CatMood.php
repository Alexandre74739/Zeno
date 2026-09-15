<?php

namespace App\Quiz;

enum CatMood: int
{
    case Angry = 0;
    case Sad = 1;
    case Neutral = 2;
    case Satisfied = 3;

    public function logo(): string
    {
        return match ($this) {
            self::Angry => 'logos/Logo-angry.svg',
            self::Sad => 'logos/Logo-sad.svg',
            self::Neutral => 'logos/Favicon.svg',
            self::Satisfied => 'logos/Logo-satisfy.svg',
        };
    }

    public function translationKey(): string
    {
        return 'learn.mood.' . strtolower($this->name);
    }

    /**
     * Correct answers needed, from 0, to level up from this tier to the next.
     * Null means this is the top tier: there is nothing further to reach.
     */
    public function progressThreshold(): ?int
    {
        return match ($this) {
            self::Angry => 1,
            self::Sad => 3,
            self::Neutral => 5,
            self::Satisfied => null,
        };
    }
}
