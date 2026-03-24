<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReservationCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::EDIT, Action::NEW);
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('serial', 'Numéro de série'),
            NumberField::new('price', 'Prix'),
            DateTimeField::new('createdDate', 'Date de création'),
            AssociationField::new('user', 'Client'),
            AssociationField::new('travelers', 'Voyageurs'),
        ];
    }
}
