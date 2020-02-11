<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
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
            ->add('firstname',TextType::class, [
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
            /* email */
            ->add('email',EmailType::class, [
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
            /* description */
            ->add('description', TextareaType::class, [
                "label" => "description",
                "attr" => [
                    'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez décrire votre problématique",
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
