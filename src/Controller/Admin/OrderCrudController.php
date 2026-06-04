<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('orderNumber', 'Numero'),
            AssociationField::new('customer', 'Client'),
            AssociationField::new('user', 'Compte client')->hideOnIndex(),
            ChoiceField::new('status', 'Statut')->setChoices([
                'En attente confirmation' => 'pending_confirmation',
                'Nouveau' => 'new',
                'Confirme' => 'confirmed',
                'Preparation' => 'preparing',
                'Expedie' => 'shipped',
                'Livre' => 'delivered',
                'Annule' => 'cancelled',
            ]),
            ChoiceField::new('shippingStatus', 'Livraison')->setChoices([
                'En attente confirmation' => 'pending_confirmation',
                'Preparation' => 'preparing',
                'Pret a expedier' => 'ready_to_ship',
                'Expedie' => 'shipped',
                'En livraison' => 'out_for_delivery',
                'Livre' => 'delivered',
                'Retour' => 'returned',
            ]),
            TextField::new('shippingMethod', 'Methode livraison')->hideOnIndex(),
            TextField::new('estimatedDelivery', 'Delai estime')->hideOnIndex(),
            TextField::new('trackingNumber', 'Tracking')->hideOnIndex(),
            TextField::new('shippingNotes', 'Notes livraison')->hideOnIndex(),
            TextField::new('paymentMethod', 'Paiement'),
            IntegerField::new('subtotal', 'Sous-total'),
            IntegerField::new('deliveryFee', 'Livraison'),
            IntegerField::new('total'),
            AssociationField::new('items')->onlyOnDetail(),
        ];
    }
}
