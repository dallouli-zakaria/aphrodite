<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Product $product = null;

    #[ORM\Column(length: 180)]
    private string $productName = '';

    #[ORM\Column(length: 40)]
    private string $size = '';

    #[ORM\Column]
    private int $quantity = 1;

    #[ORM\Column]
    private int $unitPrice = 0;

    #[ORM\Column]
    private int $lineTotal = 0;

    public function __toString(): string
    {
        return sprintf('%s (%s) x %d', $this->productName, $this->size ?: 'taille ?', $this->quantity);
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): ?Order { return $this->order; }
    public function setOrder(?Order $order): self { $this->order = $order; return $this; }
    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $product): self { $this->product = $product; return $this; }
    public function getProductName(): string { return $this->productName; }
    public function setProductName(string $productName): self { $this->productName = $productName; return $this; }
    public function getSize(): string { return $this->size; }
    public function setSize(string $size): self { $this->size = trim($size); return $this; }
    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = $quantity; return $this; }
    public function getUnitPrice(): int { return $this->unitPrice; }
    public function setUnitPrice(int $unitPrice): self { $this->unitPrice = $unitPrice; return $this; }
    public function getLineTotal(): int { return $this->lineTotal; }
    public function setLineTotal(int $lineTotal): self { $this->lineTotal = $lineTotal; return $this; }
}
