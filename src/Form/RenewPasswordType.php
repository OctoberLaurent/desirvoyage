<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/** @extends \Symfony\Component\Form\AbstractType<mixed> */
class RenewPasswordType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Ecrivez votre ancien mot de passe',
                'attr' => [
                    'class' => 'form-control mb-3',
                ],
                'mapped' => false,
                'constraints' => [
                    new SecurityAssert\UserPassword([
                        'message' => 'Votre ancien mot de passe est incorrect',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class)
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'label' => false,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
