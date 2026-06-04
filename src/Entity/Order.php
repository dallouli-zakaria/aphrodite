<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(name: 'UNIQ_ORDER_NUMBER', columns: ['order_number'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $orderNumber = null;

    #[ORM\Column(length: 40)]
    private string $status = 'pending_confirmation';

    #[ORM\Column(length: 40)]
    private string $shippingStatus = 'pending_confirmation';

    #[ORM\Column(length: 80)]
    private string $shippingMethod = 'Livraison standard';

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $trackingNumber = null;

    #[ORM\Column(length: 120)]
    private string $estimatedDelivery = '2 a 5 jours ouvrables';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $shippingNotes = null;

    #[ORM\Column(length: 40)]
    private string $paymentMethod = 'pay_on_delivery';

    #[ORM\Column]
    private int $subtotal = 0;

    #[ORM\Column]
    private int $deliveryFee = 0;

    #[ORM\Column]
    private int $total = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->orderNumber ?: sprintf('Order #%s', $this->id ?? 'new');
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomer(): ?Customer { return $this->customer; }
    public function setCustomer(?Customer $customer): self { $this->customer = $customer; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getOrderNumber(): ?string { return $this->orderNumber; }
    public function setOrderNumber(?string $orderNumber): self { $this->orderNumber = $orderNumber; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getStatusLabel(): string
    {
        return [
            'pending_confirmation' => 'En attente de confirmation',
            'new' => 'Nouveau',
            'confirmed' => 'Confirme',
            'preparing' => 'En preparation',
            'shipped' => 'Expedie',
            'delivered' => 'Livre',
            'cancelled' => 'Annule',
        ][$this->status] ?? $this->status;
    }
    public function getShippingStatus(): string { return $this->shippingStatus; }
    public function setShippingStatus(string $shippingStatus): self { $this->shippingStatus = $shippingStatus; return $this; }
    public function getShippingStatusLabel(): string
    {
        return [
            'pending_confirmation' => 'Confirmation en attente',
            'preparing' => 'Preparation',
            'ready_to_ship' => 'Pret a expedier',
            'shipped' => 'Expedie',
            'out_for_delivery' => 'En livraison',
            'delivered' => 'Livre',
            'returned' => 'Retour',
        ][$this->shippingStatus] ?? $this->shippingStatus;
    }
    public function getShippingMethod(): string { return $this->shippingMethod; }
    public function setShippingMethod(string $shippingMethod): self { $this->shippingMethod = $shippingMethod; return $this; }
    public function getTrackingNumber(): ?string { return $this->trackingNumber; }
    public function setTrackingNumber(?string $trackingNumber): self { $this->trackingNumber = $trackingNumber; return $this; }
    public function getEstimatedDelivery(): string { return $this->estimatedDelivery; }
    public function setEstimatedDelivery(string $estimatedDelivery): self { $this->estimatedDelivery = $estimatedDelivery; return $this; }
    public function getShippingNotes(): ?string { return $this->shippingNotes; }
    public function setShippingNotes(?string $shippingNotes): self { $this->shippingNotes = $shippingNotes; return $this; }
    public function getPaymentMethod(): string { return $this->paymentMethod; }
    public function setPaymentMethod(string $paymentMethod): self { $this->paymentMethod = $paymentMethod; return $this; }
    public function getSubtotal(): int { return $this->subtotal; }
    public function setSubtotal(int $subtotal): self { $this->subtotal = $subtotal; return $this; }
    public function getDeliveryFee(): int { return $this->deliveryFee; }
    public function setDeliveryFee(int $deliveryFee): self { $this->deliveryFee = $deliveryFee; return $this; }
    public function getTotal(): int { return $this->total; }
    public function setTotal(int $total): self { $this->total = $total; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getItems(): Collection { return $this->items; }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }
}
