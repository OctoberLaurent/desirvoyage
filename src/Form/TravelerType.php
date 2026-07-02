<?php

namespace App\Form;

use App\Dto\TravelerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends \Symfony\Component\Form\AbstractType<TravelerDto>
 *
 * Bind le DTO {@see TravelerDto} (skill §8) : la validation (NotBlank/Length/
 * Email/LessThan) vit sur le DTO, plus sur le champ de formulaire ni sur l'entité.
 */
final class TravelerType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
            ])
            ->add('email', EmailType::class)
            ->add('birthday', BirthdayType::class, [
                'label' => 'Votre date de naissance',
                'widget' => 'single_text',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TravelerDto::class,
        ]);
    }
}
