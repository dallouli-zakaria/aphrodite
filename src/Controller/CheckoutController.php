<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Service\CartService;
use App\Service\CatalogService;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CatalogService $catalogService,
        private readonly OrderService $orderService,
        private readonly EntityManagerInterface $entityManager,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[Route('/checkout/order', name: 'checkout_order_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('checkout_order', (string) $request->headers->get('X-CSRF-TOKEN')))) {
            return $this->json(['message' => 'Session invalide.'], 419);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['message' => 'Requete invalide.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $user = $user instanceof User ? $user : null;
        $cartItems = $user
            ? $this->cartService->snapshot($this->cartService->getOrCreateCart($user))['items']
            : (is_array($payload['items'] ?? null) ? $payload['items'] : []);

        try {
            $order = $this->orderService->createOrder($user, is_array($payload['customer'] ?? null) ? $payload['customer'] : [], $cartItems);
        } catch (\InvalidArgumentException $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($user) {
            $this->cartService->clear($user);
        }

        return $this->json([
            'message' => 'Commande creee.',
            'orderNumber' => $order->getOrderNumber(),
            'redirectUrl' => $this->generateUrl('checkout_order_completed', [
                'orderNumber' => $order->getOrderNumber(),
            ]),
        ]);
    }

    #[Route('/commande/{orderNumber}/merci', name: 'checkout_order_completed', methods: ['GET'])]
    public function completed(string $orderNumber): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $orderNumber]);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        return $this->render('Storefront/order_completed.html.twig', [
            'order' => $order,
            'categories' => $this->catalogService->findCategories(),
            'currentCategory' => null,
            'page' => 'order-completed',
        ]);
    }
}
