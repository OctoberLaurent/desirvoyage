<?php

namespace App\Form;

use App\Entity\Options;
use App\Entity\Reservation;
use App\Repository\OptionsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationOptionType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $travel = $entity->getStays()[0]->getTravel();

        $builder->add('options', EntityType::class, [
            'attr' => [
                'class' => 'check',
            ],
            'expanded' => true,
            'multiple' => true,
            'label' => false,
            'class' => Options::class,
            'query_builder' => function (OptionsRepository $repo) use ($travel) {
                return $repo->findOptions($travel);
            }, 'choice_label' => 'name',
        ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
