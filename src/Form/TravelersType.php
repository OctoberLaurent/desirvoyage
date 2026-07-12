<?php

namespace App\Form;

use App\Dto\TravelersDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @phpstan-extends \Symfony\Component\Form\AbstractType<TravelersDto> */
class TravelersType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* travelers collectionType */
            ->add('travelers', CollectionType::class, [
                'entry_type' => TravelerType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'label' => false,
                'attr' => [
                    'class' => 'custom_traveler',
                ],
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TravelersDto::class,
        ]);
    }
}
