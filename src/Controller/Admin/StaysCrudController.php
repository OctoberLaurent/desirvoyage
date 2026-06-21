<?php

namespace App\Controller\Admin;

use App\Entity\Stays;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/** @extends \EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController<Stays> */
class StaysCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Stays::class;
    }

    #[\Override]
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
            DateTimeField::new('createdDate', 'Date de création')->hideOnForm(),
        ];
    }
}
