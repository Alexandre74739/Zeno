<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'registration.prenom_label',
            ])
            ->add('nom', TextType::class, [
                'label' => 'registration.nom_label',
            ])
            ->add('telephone', TelType::class, [
                'label' => 'registration.telephone_label',
                'attr' => [
                    'maxlength' => 20,
                    'pattern' => '^\+?[0-9\s().-]{6,20}$',
                    'inputmode' => 'tel',
                    'autocomplete' => 'tel',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'registration.email_label',
                'attr' => [
                    'maxlength' => 180,
                    'autocomplete' => 'email',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'registration.password_label',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'registration.password_not_blank',
                    ),
                    new Length(
                        min: 12,
                        minMessage: 'registration.password_min_length',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                    new PasswordStrength(
                        minScore: PasswordStrength::STRENGTH_MEDIUM,
                        message: 'registration.password_too_weak',
                    ),
                ],
            ])
            ->add('rgpdConsent', CheckboxType::class, [
                'mapped' => false,
                'label_html' => true,
                'constraints' => [
                    new IsTrue(
                        message: 'registration.rgpd_consent_required',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
