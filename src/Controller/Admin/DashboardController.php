<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('Admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AphroditeShop');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToUrl('Retour boutique', 'fa fa-store', '/'),
            MenuItem::section('Catalogue'),
            MenuItem::linkToCrud('Produits', 'fa fa-shirt', Product::class),
            MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class),
            MenuItem::section('Commandes'),
            MenuItem::linkToCrud('Commandes', 'fa fa-cart-shopping', Order::class),
            MenuItem::linkToCrud('Clients', 'fa fa-user', Customer::class),
        ];
    }
}
