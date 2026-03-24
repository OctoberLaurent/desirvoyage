<?php

namespace App\Form;

use App\Entity\Pictures;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'center'],
                'label' => 'Image name'])
            ->add('url', FileUploadType::class, [
                'upload_dir' => 'public/data/',
                'upload_filename' => '[uuid].[extension]',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pictures::class,
        ]);
    }
}
