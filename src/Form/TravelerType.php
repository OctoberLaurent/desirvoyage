<?php

namespace App\Form;

use App\Entity\Traveler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TravelerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* lastname*/ 
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
            /* lastname collectiontype*/
            ->add('lastname', CollectionType::class, [
                'entry_type' => TextType::class,     
               
            ])
             /* firstname*/ 
             ->add('firstname', TextType::class, [
                "label" => "Nom",
                "attr" => [
                    'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre prenom",
                    ])
                ]
            ]) 
            /* firstname collectiontype*/
            ->add('firstname', CollectionType::class, [
                'entry_type' => TextType::class
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
            ])
            /* emailcollectiontype*/ 
            ->add('email', CollectionType::class, [
                'entry_type' => EmailType::class
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
            /* birthdaycollectiontype*/ 
            ->add('birthday', CollectionType::class, [
               'entry_type' => BirthdayType::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Traveler::class,
        ]);
    }
}
