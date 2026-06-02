<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(length: 40)]
    private string $status = 'new';

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
        return sprintf('Order #%s', $this->id ?? 'new');
    }

    public function getId(): ?int { return $this->id; }
    public function getCustomer(): ?Customer { return $this->customer; }
    public function setCustomer(?Customer $customer): self { $this->customer = $customer; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
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
}
