<?php

namespace App\Quiz;

/**
 * The quiz day is the same for every player regardless of their own timezone:
 * it resets at midnight Europe/Paris.
 */
final class QuizClock
{
    private const TIMEZONE = 'Europe/Paris';

    public function today(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable('today', new \DateTimeZone(self::TIMEZONE)))->setTime(0, 0);
    }
}
