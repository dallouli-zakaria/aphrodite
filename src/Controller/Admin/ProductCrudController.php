<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('category'),
            TextField::new('nameFr', 'Nom FR'),
            TextField::new('nameEn', 'Nom EN')->hideOnIndex(),
            TextField::new('nameAr', 'Nom AR')->hideOnIndex(),
            TextField::new('brand'),
            IntegerField::new('price', 'Prix'),
            IntegerField::new('compareAt', 'Ancien prix')->hideOnIndex(),
            TextField::new('badge'),
            TextField::new('badgeClass')->hideOnIndex(),
            NumberField::new('rating'),
            TextField::new('imageUrl')->hideOnIndex(),
            TextareaField::new('descriptionFr', 'Description FR')->hideOnIndex(),
            TextareaField::new('descriptionEn', 'Description EN')->hideOnIndex(),
            TextareaField::new('descriptionAr', 'Description AR')->hideOnIndex(),
            ArrayField::new('sizes')->hideOnIndex()->setHelp('Example: S, M, L or 50ml, 100ml'),
            BooleanField::new('active'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
