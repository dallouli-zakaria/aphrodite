<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Subcategory::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Subcategory $subcategory = null;

    #[ORM\Column(length: 160)]
    private string $nameFr = '';

    #[ORM\Column(length: 160)]
    private string $nameEn = '';

    #[ORM\Column(length: 160)]
    private string $nameAr = '';

    #[ORM\Column(length: 120)]
    private string $brand = '';

    #[ORM\Column(type: 'text')]
    private string $descriptionFr = '';

    #[ORM\Column(type: 'text')]
    private string $descriptionEn = '';

    #[ORM\Column(type: 'text')]
    private string $descriptionAr = '';

    #[ORM\Column]
    private int $price = 0;

    #[ORM\Column(nullable: true)]
    private ?int $compareAt = null;

    #[ORM\Column(length: 40)]
    private string $badge = 'New';

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $badgeClass = null;

    #[ORM\Column]
    private float $rating = 5.0;

    #[ORM\Column]
    private array $sizes = [];

    #[ORM\Column(length: 500)]
    private string $imageUrl = '';

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->nameFr;
    }

    public function getId(): ?int { return $this->id; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }
    public function getSubcategory(): ?Subcategory { return $this->subcategory; }
    public function setSubcategory(?Subcategory $subcategory): self { $this->subcategory = $subcategory; return $this; }
    public function getNameFr(): string { return $this->nameFr; }
    public function setNameFr(string $nameFr): self { $this->nameFr = $nameFr; return $this; }
    public function getNameEn(): string { return $this->nameEn; }
    public function setNameEn(string $nameEn): self { $this->nameEn = $nameEn; return $this; }
    public function getNameAr(): string { return $this->nameAr; }
    public function setNameAr(string $nameAr): self { $this->nameAr = $nameAr; return $this; }
    public function getBrand(): string { return $this->brand; }
    public function setBrand(string $brand): self { $this->brand = $brand; return $this; }
    public function getDescriptionFr(): string { return $this->descriptionFr; }
    public function setDescriptionFr(string $descriptionFr): self { $this->descriptionFr = $descriptionFr; return $this; }
    public function getDescriptionEn(): string { return $this->descriptionEn; }
    public function setDescriptionEn(string $descriptionEn): self { $this->descriptionEn = $descriptionEn; return $this; }
    public function getDescriptionAr(): string { return $this->descriptionAr; }
    public function setDescriptionAr(string $descriptionAr): self { $this->descriptionAr = $descriptionAr; return $this; }
    public function getPrice(): int { return $this->price; }
    public function setPrice(int $price): self { $this->price = $price; return $this; }
    public function getCompareAt(): ?int { return $this->compareAt; }
    public function setCompareAt(?int $compareAt): self { $this->compareAt = $compareAt; return $this; }
    public function getBadge(): string { return $this->badge; }
    public function setBadge(string $badge): self { $this->badge = $badge; return $this; }
    public function getBadgeClass(): ?string { return $this->badgeClass; }
    public function setBadgeClass(?string $badgeClass): self { $this->badgeClass = $badgeClass; return $this; }
    public function getRating(): float { return $this->rating; }
    public function setRating(float $rating): self { $this->rating = $rating; return $this; }
    public function getSizes(): array { return $this->sizes; }
    public function setSizes(array $sizes): self { $this->sizes = $sizes; return $this; }
    public function getImageUrl(): string { return $this->imageUrl; }
    public function setImageUrl(string $imageUrl): self { $this->imageUrl = $imageUrl; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): self { $this->active = $active; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
