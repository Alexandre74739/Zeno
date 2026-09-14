<?php

namespace App\Faq;

final class FaqProvider
{
    /**
     * @return FaqItem[]
     */
    public function getItems(): array
    {
        return [
            new FaqItem('faq.item1_question', 'faq.item1_answer'),
            new FaqItem('faq.item2_question', 'faq.item2_answer'),
            new FaqItem('faq.item3_question', 'faq.item3_answer'),
            new FaqItem('faq.item4_question', 'faq.item4_answer'),
            new FaqItem('faq.item5_question', 'faq.item5_answer'),
        ];
    }
}
