<?php

namespace App\Form;

use App\Entity\Picture;
use App\Form\Type\PublicImageUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @phpstan-extends \Symfony\Component\Form\AbstractType<Picture> */
class PictureType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'center'],
                'label' => 'Image name'])
            ->add('url', PublicImageUploadType::class, [
                'upload_dir' => 'public/',
                'upload_filename' => 'data/[uuid].[extension]',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
