<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class EditUserType extends AbstractType
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
                    'constraints' => [
                        new NotBlank([
                            'message' => "Saisir votre nom",
                        ])
                    ]
            ])
            /* firstname */ 
            ->add('firstname', TextType::class, [
                    "label" => "Prénom",
                    "attr" => [
                        'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre prénom",
                    ])
                ]
            ])
            /* birthday */ 
            ->add('birthday', BirthdayType::class, [
                "label" => "date de naissance",
                "widget" =>'single_text',
                "attr" => [
                    'class' => "form-control",
                    'format' => 'yyyy-MM-dd',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre date de naissance",
                    ])
                ]
            ])
            /* adresse */
            ->add('address', TextType::class, [
                'label' => 'adresse',
                "attr" => [
                    'class' => "adresse autocomplete",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre adresse",
                    ])
                ]
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
                "attr" => [
                    'class' => 'postalcode autocomplete',
                ],
                'label_attr' => [
                    'class' => 'active'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre code postale",
                    ])
                ]
            ])
            /* ville */
            ->add('city', TextType::class, [
                'label' => 'ville',
                "attr" => [
                    
                    
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre ville",
                    ])
                ]
            ])
            /* country */
            ->add('country', CountryType::class, [
                "label" => "pays",
                "attr" => [
                    'class'=> "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir le pays",
                    ])
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
                    'constraints' => [
                        new NotBlank([
                            'message' => "Saisir votre email",
                        ])
                    ]
            ]);   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
