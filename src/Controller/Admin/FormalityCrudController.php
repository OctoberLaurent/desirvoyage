<?php

namespace App\Controller\Admin;

use App\Entity\Formality;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class FormalityCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Formality::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            CountryField::new('destination', 'Pays'),
            TextEditorField::new('description', 'Description'),
        ];
    }
}
