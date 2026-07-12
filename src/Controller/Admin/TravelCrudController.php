<?php

namespace App\Controller\Admin;

use App\Entity\Travel;
use App\Form\PictureType;
use App\Form\StayType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController<Travel> */
class TravelCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Travel::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            TextField::new('subtitle', 'Sous-titre')->hideOnIndex(),
            TextEditorField::new('descriptions', 'Description')->hideOnIndex(),
            CollectionField::new('pictures', 'Images')->setEntryType(PictureType::class)->hideOnIndex(),
            CollectionField::new('stays', 'Séjours')->setEntryType(StayType::class)->hideOnIndex(),
            AssociationField::new('category', 'Catégorie'),
            AssociationField::new('options', 'Option')->hideOnIndex(),
            AssociationField::new('formalities', 'Formalités')->hideOnIndex(),
        ];
    }
}
