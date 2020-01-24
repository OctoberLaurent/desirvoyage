<?php

namespace App\Form;

use App\Entity\Stays;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class StayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('starDate', DateType::class, [
            'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            ])
            ->add('depature', TextType::class)
            ->add('arrival', TextType::class)
            ->add('price', MoneyType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stays::class,
        ]);
    }
}
