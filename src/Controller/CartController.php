<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly EntityManagerInterface $entityManager,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/data', name: 'data', methods: ['GET'])]
    public function data(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Connexion requise.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($this->cartService->snapshot($this->cartService->getOrCreateCart($user)));
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        if (!$this->isValidCartToken($request)) {
            return $this->json(['message' => 'Session invalide.'], 419);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Connexion requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $this->payload($request);
        $productId = isset($payload['productId']) ? (int) $payload['productId'] : 0;
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if (!$product instanceof Product || !$product->isActive()) {
            return $this->json(['message' => 'Produit introuvable.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $cart = $this->cartService->addProduct($user, $product, (string) ($payload['size'] ?? ''));
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($this->cartService->snapshot($cart));
    }

    #[Route('/merge', name: 'merge', methods: ['POST'])]
    public function merge(Request $request): JsonResponse
    {
        if (!$this->isValidCartToken($request)) {
            return $this->json(['message' => 'Session invalide.'], 419);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Connexion requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $this->payload($request);
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];
        $cart = $this->cartService->mergeItems($user, $items);

        return $this->json($this->cartService->snapshot($cart));
    }

    #[Route('/remove', name: 'remove', methods: ['POST'])]
    public function remove(Request $request): JsonResponse
    {
        if (!$this->isValidCartToken($request)) {
            return $this->json(['message' => 'Session invalide.'], 419);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Connexion requise.'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = $this->payload($request);
        $productId = isset($payload['productId']) ? (int) $payload['productId'] : 0;
        $cart = $this->cartService->removeProduct($user, $productId, (string) ($payload['size'] ?? ''));

        return $this->json($this->cartService->snapshot($cart));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request): array
    {
        $decoded = json_decode($request->getContent(), true);

        return is_array($decoded) ? $decoded : $request->request->all();
    }

    private function isValidCartToken(Request $request): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken('cart', (string) $request->headers->get('X-CSRF-TOKEN')));
    }
}
