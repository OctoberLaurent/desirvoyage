<?php

namespace App\Form;

use App\Entity\Traveler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class TravelerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* lastname*/ 
            ->add('lastname', TextType::class, [
                "attr" => [
                    // "class" => "col s6",
                ],
                "label" => 'Votre nom',
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre nom",
                    ]),
                ]
            ]) 
             /* firstname*/ 
             ->add('firstname', TextType::class, [
                "attr" => [
                    // "class" => "col s6" 
                ],
                "label" => 'Votre prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre prenom",
                    ])
                ]
            ]) 
            /* email */ 
            ->add('email', EmailType::class, [
                "attr" => [
                    // "class" => "col s6"
                ],
                'constraints' => [

                    new NotBlank([
                        'message' => "Saisir votre email",
                    ])
                ]
            ])
            /* birthday */ 
            ->add('birthday', BirthdayType::class, [
                "attr" => [
                // "class" => "col s6" 
                ],
                "label" => 'Votre date de naissance',
                "widget" =>'single_text',
               
                'constraints' => [
                  new NotBlank([
                        'message' => "Saisir votre date de naissance",
                    ])
                ]
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
