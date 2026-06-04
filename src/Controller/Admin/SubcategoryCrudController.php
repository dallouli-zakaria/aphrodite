<?php

namespace App\Controller\Admin;

use App\Entity\Subcategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class SubcategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Subcategory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('category', 'Categorie'),
            TextField::new('slug'),
            TextField::new('nameFr', 'Nom FR'),
            TextField::new('nameEn', 'Nom EN'),
            TextField::new('nameAr', 'Nom AR')->hideOnIndex(),
            IntegerField::new('position'),
            TextField::new('imageUrl')->hideOnIndex(),
            TextareaField::new('descriptionFr', 'Description FR')->hideOnIndex(),
            TextareaField::new('descriptionEn', 'Description EN')->hideOnIndex(),
            TextareaField::new('descriptionAr', 'Description AR')->hideOnIndex(),
        ];
    }
}
