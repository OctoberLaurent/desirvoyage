<?php

namespace App\Controller\Admin;

use App\Entity\Stays;
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

class StaysCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stays::class;
    }



    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('travel', 'Voyage'),
            TextField::new('depature', 'Ville de départ'),
            TextField::new('arrival', 'Ville d\'arrivée'),
            DateTimeField::new('starDate', 'Date de départ'),
            DateTimeField::new('endDate', 'Date de retour'),
            NumberField::new('stock', 'Stock'),
            NumberField::new('price', 'Prix'),
            TextField::new('serial', 'Numéro de série')->hideOnForm(),
            DateTimeField::new('createdDate', 'Date de création')->hideOnForm()
        ];
    }
}
