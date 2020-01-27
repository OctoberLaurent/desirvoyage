<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* lastname */ 
            ->add('lastname', TextType::class, [
                    "label" => "Nom",
                    "attr" => [
                        'class' => "form-control",
                    ],
            ])
            /* firstname */ 
            ->add('firstname', TextType::class, [
                    "label" => "Prénom",
                    "attr" => [
                        'class' => "form-control",
                ],
            ])
            /* birthday */ 
            ->add('birthday', BirthdayType::class, [
                "label" => "date de naissance",
                "widget" =>'single_text',
                "attr" => [
                    'class' => "form-control",
                    'format' => 'yyyy-MM-dd',
                ]
            ])
            /* adresse */
            ->add('address', TextType::class, [
                'label' => "adresse",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            /* complément d'adresse */
            ->add('additionalAddress', TextType::class, [
                'required' => false,
                'label' => "complément d'adresse",
                "attr" => [
                    'class' => "form-control",
                    
                ],
            ])
            /* code postale */ 
            ->add('postalCode', NumberType::class, [
                'label' => "code postale",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            /* ville */
            ->add('city', TextType::class, [
                'label' => "ville",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            /* country */
            ->add('country', CountryType::class, [
                "label" => "pays",
                "attr" => [
                    'class'=> "form-control",
                ]
            ])
            /* fixe */
            ->add('phone', TextType::class, [
                "label" => "telephone",
                "attr" => [
                    'class'=> "form-control",
                ]
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
            
            
        
           
            
          
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
