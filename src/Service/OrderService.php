<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private const FREE_SHIPPING_LIMIT = 599;
    private const SHIPPING_COST = 29;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array<string, mixed> $customerData
     * @param array<int, array{id?: mixed, size?: mixed, qty?: mixed}> $cartItems
     */
    public function createOrder(?User $user, array $customerData, array $cartItems): Order
    {
        if ($cartItems === []) {
            throw new \InvalidArgumentException('Votre panier est vide.');
        }

        $customer = (new Customer())
            ->setFullName($this->required($customerData, 'fullName', 'Le nom complet est obligatoire.'))
            ->setPhone($this->required($customerData, 'phone', 'Le telephone est obligatoire.'))
            ->setEmail($this->optional($customerData, 'email') ?: $user?->getEmail())
            ->setCity($this->required($customerData, 'city', 'La ville est obligatoire.'))
            ->setDistrict($this->optional($customerData, 'district'))
            ->setAddress($this->required($customerData, 'address', 'L adresse est obligatoire.'));

        $order = (new Order())
            ->setCustomer($customer)
            ->setUser($user)
            ->setOrderNumber($this->generateOrderNumber())
            ->setStatus('pending_confirmation')
            ->setShippingStatus('pending_confirmation')
            ->setShippingMethod('Livraison standard')
            ->setEstimatedDelivery('2 a 5 jours ouvrables')
            ->setShippingNotes('Commande recue. Confirmation client et preparation en attente.')
            ->setPaymentMethod('pay_on_delivery');

        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            $productId = isset($cartItem['id']) ? (int) $cartItem['id'] : 0;
            $quantity = max(1, isset($cartItem['qty']) ? (int) $cartItem['qty'] : 1);
            $size = trim((string) ($cartItem['size'] ?? ''));
            if ($productId <= 0 || $size === '') {
                throw new \InvalidArgumentException('Veuillez choisir une taille pour chaque article.');
            }

            $product = $this->entityManager->getRepository(Product::class)->find($productId);
            if (!$product instanceof Product || !$product->isActive()) {
                throw new \InvalidArgumentException('Un article du panier nest plus disponible.');
            }

            $availableSizes = array_map(static fn (mixed $value): string => trim((string) $value), $product->getSizes());
            if (!in_array($size, $availableSizes, true)) {
                throw new \InvalidArgumentException(sprintf('La taille %s nest pas disponible pour %s.', $size, $product->getNameFr()));
            }

            $lineTotal = $product->getPrice() * $quantity;
            $subtotal += $lineTotal;
            $order->addItem((new OrderItem())
                ->setProduct($product)
                ->setProductName($product->getNameFr())
                ->setSize($size)
                ->setQuantity($quantity)
                ->setUnitPrice($product->getPrice())
                ->setLineTotal($lineTotal));
        }

        $delivery = $subtotal === 0 || $subtotal >= self::FREE_SHIPPING_LIMIT ? 0 : self::SHIPPING_COST;
        $order
            ->setSubtotal($subtotal)
            ->setDeliveryFee($delivery)
            ->setTotal($subtotal + $delivery);

        $this->entityManager->persist($customer);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'AS-'.date('ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
        } while ($this->entityManager->getRepository(Order::class)->findOneBy(['orderNumber' => $number]));

        return $number;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function required(array $data, string $key, string $message): string
    {
        $value = trim((string) ($data[$key] ?? ''));
        if ($value === '') {
            throw new \InvalidArgumentException($message);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function optional(array $data, string $key): ?string
    {
        $value = trim((string) ($data[$key] ?? ''));

        return $value === '' ? null : $value;
    }
}
