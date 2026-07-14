<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Type\PublicImageUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController<Category> */
class CategoryCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            ImageField::new('url', 'Image')
                ->setFormType(PublicImageUploadType::class)
                ->setUploadDir('public/')
                ->setUploadedFileNamePattern('data/[uuid].[extension]'),
            AssociationField::new('travel', 'Voyages'),
        ];
    }
}
