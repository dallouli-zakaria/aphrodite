<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('slug'),
            TextField::new('nameFr', 'Nom FR'),
            TextField::new('nameEn', 'Nom EN'),
            TextField::new('nameAr', 'Nom AR'),
            TextField::new('imageUrl')->hideOnIndex(),
            TextareaField::new('descriptionFr', 'Description FR')->hideOnIndex(),
            TextareaField::new('descriptionEn', 'Description EN')->hideOnIndex(),
            TextareaField::new('descriptionAr', 'Description AR')->hideOnIndex(),
        ];
    }
}
