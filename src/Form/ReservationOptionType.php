<?php

namespace App\Form;

use App\Entity\Options;
use App\Entity\Reservation;
use App\Repository\OptionsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends \Symfony\Component\Form\AbstractType<Reservation> */
class ReservationOptionType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $travelId = $options['travel_id'];

        if (!is_int($travelId)) {
            return;
        }

        $builder->add('options', EntityType::class, [
            'attr' => [
                'class' => 'check',
            ],
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'class' => Options::class,
            'query_builder' => fn (OptionsRepository $repo) => $repo->findOptions($travelId), 'choice_label' => 'name',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'travel_id' => null,
        ]);
    }
}
