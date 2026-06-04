<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Subcategory;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('styles/admin.css');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToUrl('Retour boutique', 'fa fa-store', '/'),
            MenuItem::section('Catalogue'),
            MenuItem::linkTo(ProductCrudController::class, 'Produits', 'fa fa-shirt'),
            MenuItem::linkTo(CategoryCrudController::class, 'Categories', 'fa fa-tags'),
            MenuItem::linkTo(SubcategoryCrudController::class, 'Sous-categories', 'fa fa-list'),
            MenuItem::section('Commandes'),
            MenuItem::linkTo(OrderCrudController::class, 'Commandes', 'fa fa-cart-shopping'),
            MenuItem::linkTo(CustomerCrudController::class, 'Clients', 'fa fa-user'),
            MenuItem::linkTo(UserCrudController::class, 'Comptes', 'fa fa-user-lock'),
        ];
    }
}
