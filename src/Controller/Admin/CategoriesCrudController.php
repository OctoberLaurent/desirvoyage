<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoriesCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Categories::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            ImageField::new('url', 'Image')
                ->setUploadDir('public/data/')
                ->setUploadedFileNamePattern('[uuid].[extension]')
                ->setBasePath('data/'),
            AssociationField::new('travel', 'Voyages'),
        ];
    }
}
