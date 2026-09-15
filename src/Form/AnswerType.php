<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuestionChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Question $question */
        $question = $options['question'];

        $builder->add('choice', EntityType::class, [
            'class' => QuestionChoice::class,
            'choices' => $question->getChoices(),
            'choice_label' => 'label',
            'expanded' => true,
            'multiple' => false,
            'label' => false,
            'choice_translation_domain' => false,
            'constraints' => [
                new NotNull(message: 'learn.error.choice_required'),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => null])
            ->setRequired('question')
            ->setAllowedTypes('question', Question::class)
        ;
    }
}
