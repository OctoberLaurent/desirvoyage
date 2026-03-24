<?php

namespace App\Controller\Admin;

use App\Entity\Options;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OptionsCrudController extends AbstractCrudController
{
    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Options::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            TextEditorField::new('description', 'Description'),
            NumberField::new('price', 'Prix'),
            TextField::new('type', 'Type'),
        ];
    }
}
