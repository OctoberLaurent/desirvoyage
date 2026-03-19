<?php

namespace App\Controller\Admin;

use App\Entity\Travel;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use App\Form\PictureType;
use App\Form\StayType;

class TravelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Travel::class;
    }



    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            TextField::new('subtitle', 'Sous-titre')->hideOnIndex(),
            TextEditorField::new('descriptions', 'Description')->hideOnIndex(),
            CollectionField::new('pictures', 'Images')->setEntryType(PictureType::class)->hideOnIndex(),
            CollectionField::new('stays', 'Séjours')->setEntryType(StayType::class)->hideOnIndex(),
            AssociationField::new('categories', 'Catégories'),
            AssociationField::new('options', 'Options')->hideOnIndex(),
            AssociationField::new('formality', 'Formalités')->hideOnIndex()
        ];
    }
}
