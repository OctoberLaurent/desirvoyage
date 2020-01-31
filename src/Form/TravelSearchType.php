<?php

namespace App\Form;

use App\Entity\Formality;
use Symfony\Component\Intl\Intl;
use Doctrine\DBAL\Types\FloatType;
use App\Repository\TravelRepository;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
\Locale::setDefault('fr');

class TravelSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('search', TextType::class, [ 
                'label' => 'What are you looking for ?',
                'required' => false,
                'attr' => [
                    'class' => 'validate'
                ]
             ])

             ->add('maxprice', RangeType::class, [ 
                'label' => 'Maximum price',
                'label_attr' => 
                [
                    'id' => 'maxprice'
                ],
                'attr' => [
                  'min' => 0, 
                  'max' => 10000
                ]
             ])

             ->add('country', EntityType::class, [
                'class' => Formality::class,
                'choice_label' => function( $formality ){
                    return Countries::getName( $formality->getDestination() );
                },
              ])

             ->add('startdate', DateType::class, [ 
                'label' => 'Séjour entre le ',
                'html5' => false,
                "widget" =>'single_text',
                'attr' => [
                    'class' => 'datepicker',
                ]
             ])

             ->add('enddate', DateType::class, [ 
                'label' => 'et le ...',
                'html5' => false,
                "widget" =>'single_text',
                'attr' => [
                    'class' => 'datepicker',
                ]
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
