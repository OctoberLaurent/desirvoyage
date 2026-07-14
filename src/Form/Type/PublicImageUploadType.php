<?php

namespace App\Form\Type;

use App\Form\DataTransformer\PublicImagePathTransformer;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\FormBuilderInterface;

final class PublicImageUploadType extends FileUploadType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->addModelTransformer(new PublicImagePathTransformer());
    }
}
