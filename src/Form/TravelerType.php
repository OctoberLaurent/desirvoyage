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
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre nom",
                    ])
                ]
            ]) 
             /* firstname*/ 
             ->add('firstname', TextType::class, [
                "label" => "Prénom",
              
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre prenom",
                    ])
                ]
            ]) 
           
            /* email */ 
            ->add('email', EmailType::class, [
                "label" => "Email",
              
                'constraints' => [
                    new NotBlank([
                        'message' => "Saisir votre email",
                    ])
                ]
            ])

            /* birthday */ 
            ->add('birthday', BirthdayType::class, [
                "label" => "date de naissance",
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
