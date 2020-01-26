<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* firstname */ 
            ->add('firstname', TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            /* lastname */ 
            ->add('lastname', TextType::class, [
                "label" => "Nom",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            /* email */
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            /* Mot de passe */
            ->add('password', RepeatedType::class, [
                'label' => false,
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => "mot de passe",
                    'required' => true,
                    'constraints' => [
                        new NotNull([
                            'message' => "Saisir votre mot de passe",
                        ]),
                        new NotBlank([
                            'message' => "Saisir votre mot de passe",
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => "Repéter le mot de passe",
                    'constraints' => [
                        new NotBlank([
                            'message' => "Repéter le mot de passe",
                        ]),
                    ],
                ],
                'invalid_message' => "Les mots de passe doivent etre identiques.",
            ])

            /* Accepte les conditions d'utilisation */
            ->add('agreeTerms', CheckboxType::class, [
            'label' => false,
            "attr" => [
                "class" => "filled-in",
                'checked' => 'checked'
            ],
            'mapped' => false, // ce champ n'est pas dans l'entité User
        ]);          
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
