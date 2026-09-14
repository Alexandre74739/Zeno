<?php

namespace App\Faq;

final readonly class FaqItem
{
    public function __construct(
        public string $questionKey,
        public string $answerKey,
    ) {
    }
}
