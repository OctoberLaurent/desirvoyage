<?php

namespace App\Controller\Admin;

use App\Entity\User;
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

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }



    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            EmailField::new('email', 'Email'),
            TelephoneField::new('phone', 'Téléphone'),
            BooleanField::new('enabled', 'Actif'),
            CountryField::new('country', 'Pays')->hideOnIndex(),
            TextField::new('password', 'Mot de passe')
                ->hideOnIndex()
                ->setDisabled()
                ->setFormTypeOption('mapped', false)
                ->setRequired(false),
            ChoiceField::new('roles', 'Rôles')
                ->allowMultipleChoices()
                ->setChoices(['Client' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'])
                ->hideOnIndex(),
            TextField::new('address', 'Adresse')->hideOnIndex(),
            TextField::new('additionalAddress', 'Complément')->hideOnIndex(),
            TextField::new('postalCode', 'Code postal')->hideOnIndex(),
            TextField::new('city', 'Ville')->hideOnIndex(),
            DateField::new('birthday', 'Date de naissance')->hideOnIndex()
        ];
    }
}
