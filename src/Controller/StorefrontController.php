<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Service\CatalogService;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StorefrontController extends AbstractController
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly CartService $cartService,
    ) {
    }

    #[Route('/', name: 'storefront_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->renderStorefront('home', [
            'title' => 'AphroditeShop',
            'eyebrow' => 'Nouvelle collection',
            'heading' => 'Mode, sneakers, parfums et accessoires.',
            'intro' => 'Explorez les univers Hommes et Femmes, puis filtrez par sous-categorie.',
            'products' => $this->catalogService->findProducts(limit: 8),
            'subcategories' => [],
        ]);
    }

    #[Route('/categorie/{slug}', name: 'storefront_category', methods: ['GET'])]
    public function category(string $slug): Response
    {
        $category = $this->catalogService->findCategory($slug);
        if (!$category) {
            throw $this->createNotFoundException('Categorie introuvable.');
        }

        return $this->renderStorefront('category', [
            'title' => $category->getNameFr(),
            'eyebrow' => 'Categorie',
            'heading' => $category->getNameFr(),
            'intro' => $category->getDescriptionFr() ?: 'Selection AphroditeShop par categorie.',
            'currentCategory' => $category,
            'products' => $this->catalogService->findProducts(category: $category),
            'subcategories' => $category->getSubcategories(),
        ]);
    }

    #[Route('/categorie/{categorySlug}/{subcategorySlug}', name: 'storefront_subcategory', methods: ['GET'])]
    public function subcategory(string $categorySlug, string $subcategorySlug): Response
    {
        $category = $this->catalogService->findCategory($categorySlug);
        if (!$category) {
            throw $this->createNotFoundException('Categorie introuvable.');
        }

        $subcategory = $this->catalogService->findSubcategory($category, $subcategorySlug);
        if (!$subcategory) {
            throw $this->createNotFoundException('Sous-categorie introuvable.');
        }

        return $this->renderStorefront('subcategory', [
            'title' => $category->getNameFr().' / '.$subcategory->getNameFr(),
            'eyebrow' => $category->getNameFr(),
            'heading' => $subcategory->getNameFr(),
            'intro' => $subcategory->getDescriptionFr() ?: $category->getDescriptionFr(),
            'currentCategory' => $category,
            'currentSubcategory' => $subcategory,
            'products' => $this->catalogService->findProducts(category: $category, subcategory: $subcategory),
            'subcategories' => $category->getSubcategories(),
        ]);
    }

    #[Route('/new-in', name: 'storefront_new_in', methods: ['GET'])]
    public function newIn(): Response
    {
        return $this->renderStorefront('new-in', [
            'title' => 'New In',
            'eyebrow' => 'Nouveautes',
            'heading' => 'Les dernieres arrivees.',
            'intro' => 'Les produits recemment ajoutes au catalogue.',
            'products' => $this->catalogService->findProducts(limit: 12),
            'subcategories' => [],
        ]);
    }

    #[Route('/checkout', name: 'storefront_checkout', methods: ['GET'])]
    public function checkout(): Response
    {
        return $this->renderStorefront('checkout', [
            'title' => 'Checkout',
            'eyebrow' => 'Votre commande',
            'heading' => 'Panier et checkout.',
            'intro' => 'Verifiez vos articles et renseignez les informations de livraison.',
            'products' => [],
            'subcategories' => [],
        ]);
    }

    #[Route('/produit/{id}/{slug}', name: 'storefront_product', requirements: ['id' => '\d+'], defaults: ['slug' => ''], methods: ['GET'])]
    public function product(Product $product, string $slug = ''): Response
    {
        if (!$product->isActive()) {
            throw $this->createNotFoundException('Produit introuvable.');
        }

        $expectedSlug = $this->catalogService->slugify($product->getNameFr());
        if ($slug !== $expectedSlug) {
            return $this->redirectToRoute('storefront_product', [
                'id' => $product->getId(),
                'slug' => $expectedSlug,
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->renderStorefront('product', [
            'title' => $product->getNameFr(),
            'eyebrow' => $product->getCategory()?->getNameFr() ?? 'Produit',
            'heading' => $product->getNameFr(),
            'intro' => $product->getDescriptionFr(),
            'product' => $product,
            'products' => [],
            'similarProducts' => $this->catalogService->findSimilarProducts($product, 8),
            'currentCategory' => $product->getCategory(),
            'currentSubcategory' => $product->getSubcategory(),
            'subcategories' => $product->getCategory()?->getSubcategories() ?? [],
        ]);
    }

    private function renderStorefront(string $page, array $context): Response
    {
        $user = $this->getUser();
        $cart = $user instanceof User ? $this->cartService->getOrCreateCart($user) : null;

        return $this->render('Storefront/index.html.twig', [
            'page' => $page,
            'categories' => $this->catalogService->findCategories(),
            'authenticatedCart' => $this->cartService->snapshot($cart),
            'currentCategory' => $context['currentCategory'] ?? null,
            'currentSubcategory' => $context['currentSubcategory'] ?? null,
            'product' => $context['product'] ?? null,
            'similarProducts' => $context['similarProducts'] ?? [],
            ...$context,
        ]);
    }
}
