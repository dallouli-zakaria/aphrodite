<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private const FREE_SHIPPING_LIMIT = 599;
    private const SHIPPING_COST = 29;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getOrCreateCart(User $user): Cart
    {
        $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['user' => $user]);
        if ($cart instanceof Cart) {
            return $cart;
        }

        $cart = (new Cart())->setUser($user);
        $this->entityManager->persist($cart);

        return $cart;
    }

    public function addProduct(User $user, Product $product, string $size, int $quantity = 1): Cart
    {
        $size = $this->normalizeSize($product, $size);
        $cart = $this->getOrCreateCart($user);
        $item = $this->findItem($cart, $product, $size);

        if ($item) {
            $item->setQuantity($item->getQuantity() + max(1, $quantity));
        } else {
            $item = (new CartItem())
                ->setProduct($product)
                ->setSize($size)
                ->setQuantity(max(1, $quantity));
            $cart->addItem($item);
            $this->entityManager->persist($item);
        }

        $cart->touch();
        $this->entityManager->flush();

        return $cart;
    }

    /**
     * @param array<int, array{id?: mixed, size?: mixed, qty?: mixed}> $items
     */
    public function mergeItems(User $user, array $items): Cart
    {
        $cart = $this->getOrCreateCart($user);

        foreach ($items as $item) {
            $productId = isset($item['id']) ? (int) $item['id'] : 0;
            $quantity = isset($item['qty']) ? (int) $item['qty'] : 1;
            if ($productId <= 0) {
                continue;
            }

            $product = $this->entityManager->getRepository(Product::class)->find($productId);
            if (!$product instanceof Product || !$product->isActive()) {
                continue;
            }

            try {
                $size = $this->normalizeSize($product, (string) ($item['size'] ?? ''));
            } catch (\InvalidArgumentException) {
                continue;
            }

            $cartItem = $this->findItem($cart, $product, $size);
            if ($cartItem) {
                $cartItem->setQuantity($cartItem->getQuantity() + max(1, $quantity));
            } else {
                $cartItem = (new CartItem())
                    ->setProduct($product)
                    ->setSize($size)
                    ->setQuantity(max(1, $quantity));
                $cart->addItem($cartItem);
                $this->entityManager->persist($cartItem);
            }
        }

        $cart->touch();
        $this->entityManager->flush();

        return $cart;
    }

    public function removeProduct(User $user, int $productId, string $size): Cart
    {
        $cart = $this->getOrCreateCart($user);
        $size = trim($size);

        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()?->getId() === $productId && $item->getSize() === $size) {
                $cart->removeItem($item);
                $this->entityManager->remove($item);
                break;
            }
        }

        $cart->touch();
        $this->entityManager->flush();

        return $cart;
    }

    public function clear(User $user): Cart
    {
        $cart = $this->getOrCreateCart($user);

        foreach ([...$cart->getItems()] as $item) {
            $cart->removeItem($item);
            $this->entityManager->remove($item);
        }

        $cart->touch();
        $this->entityManager->flush();

        return $cart;
    }

    /**
     * @return array{items: list<array{id: int, key: string, name: string, price: int, image: string, size: string, qty: int}>, count: int, subtotal: int, delivery: int, total: int}
     */
    public function snapshot(?Cart $cart): array
    {
        $items = [];
        $subtotal = 0;
        $count = 0;

        if ($cart) {
            foreach ($cart->getItems() as $item) {
                $product = $item->getProduct();
                if (!$product instanceof Product || !$product->isActive()) {
                    continue;
                }

                $quantity = $item->getQuantity();
                $size = $item->getSize();
                $items[] = [
                    'id' => (int) $product->getId(),
                    'key' => $product->getId().':'.$size,
                    'name' => $product->getNameFr(),
                    'price' => $product->getPrice(),
                    'image' => $product->getImageUrl(),
                    'size' => $size,
                    'qty' => $quantity,
                ];
                $subtotal += $product->getPrice() * $quantity;
                $count += $quantity;
            }
        }

        $delivery = $subtotal === 0 || $subtotal >= self::FREE_SHIPPING_LIMIT ? 0 : self::SHIPPING_COST;

        return [
            'items' => $items,
            'count' => $count,
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
        ];
    }

    private function findItem(Cart $cart, Product $product, string $size): ?CartItem
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()?->getId() === $product->getId() && $item->getSize() === $size) {
                return $item;
            }
        }

        return null;
    }

    private function normalizeSize(Product $product, string $size): string
    {
        $size = trim($size);
        $availableSizes = array_map(static fn (mixed $value): string => trim((string) $value), $product->getSizes());

        if ($size === '' || !in_array($size, $availableSizes, true)) {
            throw new \InvalidArgumentException('Veuillez choisir une taille.');
        }

        return $size;
    }
}
