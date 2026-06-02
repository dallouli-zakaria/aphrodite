<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StorefrontController extends AbstractController
{
    #[Route('/', name: 'storefront_home', methods: ['GET'])]
    #[Route(
        '/{path}',
        name: 'storefront_fallback',
        requirements: ['path' => '(?!admin|_profiler|_wdt|assets|favicon\.ico).*'],
        methods: ['GET']
    )]
    public function index(): Response
    {
        return $this->render('Storefront/index.html.twig');
    }
}
