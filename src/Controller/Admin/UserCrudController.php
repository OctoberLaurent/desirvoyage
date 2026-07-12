<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController<User> */
class UserCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname', 'Prénom'),
            TextField::new('lastname', 'Nom'),
            EmailField::new('email', 'Email'),
            TelephoneField::new('phone', 'Téléphone'),
            BooleanField::new('enabled', 'Actif'),
            CountryField::new('country', 'Pays')->hideOnIndex(),
            ChoiceField::new('roles', 'Rôles')
                ->allowMultipleChoices()
                ->setChoices(['Client' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'])
                ->hideOnIndex(),
            TextField::new('address', 'Adresse')->hideOnIndex(),
            TextField::new('additionalAddress', 'Complément')->hideOnIndex(),
            TextField::new('postalCode', 'Code postal')->hideOnIndex(),
            TextField::new('city', 'Ville')->hideOnIndex(),
            DateField::new('birthday', 'Date de naissance')->hideOnIndex(),
        ];
    }
}
