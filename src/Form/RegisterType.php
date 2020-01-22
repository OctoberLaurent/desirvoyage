<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                "label" => "firstname",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('lastname', TextType::class, [
                "label" => "lastname",
                "attr" => [
                    'class' => "form-control",
                ],
            ])
            ->add('email', EmailType::class, [
                "label" => "Email",
                "attr" => [
                    "class" => "form-control",
                ],
            ])
            ->add('password', PasswordType::class, [
                "label" => "password",
                "constraints" => [
                    new Regex([
                        "message" => "Don't use spaces in your password."
                    ]) 
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
