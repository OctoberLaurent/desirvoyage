<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TravelersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /* travelers collectionType */
            ->add('travelers', CollectionType::class, [
                'entry_type' => TravelerType::class,
                'allow_add'=> true,
                'allow_delete' => true,
                'entry_options'=> [
                    'label' => false,
                ],
                'label' => false,
                'attr' => [
                    'class' => 'custom_traveler'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,  
        ]);
    }
}