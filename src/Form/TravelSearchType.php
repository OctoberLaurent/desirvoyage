<?php

namespace App\Form;

use App\Entity\Formality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @phpstan-extends \Symfony\Component\Form\AbstractType<mixed> */
class TravelSearchType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        \Locale::setDefault('fr');
        $builder
            ->add('search', TextType::class, [
                'label' => 'What are you looking for ?',
                'required' => false,
                'attr' => [
                    'class' => 'validate',
                ],
            ])

             ->add('maxprice', RangeType::class, [
                 'label' => 'Maximum price',
                 'label_attr' => [
                     'id' => 'maxprice',
                 ],
                 'attr' => [
                     'min' => 0,
                     'max' => 10000,
                 ],
             ])

             ->add('country', EntityType::class, [
                 'required' => false,
                 'class' => Formality::class,
                 'choice_label' => fn (Formality $formality): string => Countries::getName($formality->getDestination()),
             ])

             ->add('startdate', DateType::class, [
                 'required' => false,
                 'label' => 'Séjour entre le ',
                 'html5' => false,
                 'widget' => 'single_text',
                 'attr' => [
                     'class' => 'datepicker',
                 ],
             ])

             ->add('enddate', DateType::class, [
                 'required' => false,
                 'label' => 'et le ...',
                 'html5' => false,
                 'widget' => 'single_text',
                 'attr' => [
                     'class' => 'datepicker',
                 ],
             ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
