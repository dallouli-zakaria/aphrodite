<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subcategories')]
#[ORM\UniqueConstraint(name: 'uniq_subcategory_parent_slug', columns: ['category_id', 'slug'])]
class Subcategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'subcategories')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Category $category = null;

    #[ORM\Column(length: 80)]
    private string $slug = '';

    #[ORM\Column(length: 120)]
    private string $nameFr = '';

    #[ORM\Column(length: 120)]
    private string $nameEn = '';

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $nameAr = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptionFr = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptionAr = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $position = 0;

    #[ORM\OneToMany(mappedBy: 'subcategory', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->category ? $this->category.' / '.$this->nameFr : $this->nameFr;
    }

    public function getId(): ?int { return $this->id; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
    public function getNameFr(): string { return $this->nameFr; }
    public function setNameFr(string $nameFr): self { $this->nameFr = $nameFr; return $this; }
    public function getNameEn(): string { return $this->nameEn; }
    public function setNameEn(string $nameEn): self { $this->nameEn = $nameEn; return $this; }
    public function getNameAr(): ?string { return $this->nameAr; }
    public function setNameAr(?string $nameAr): self { $this->nameAr = $nameAr; return $this; }
    public function getDescriptionFr(): ?string { return $this->descriptionFr; }
    public function setDescriptionFr(?string $descriptionFr): self { $this->descriptionFr = $descriptionFr; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $descriptionEn): self { $this->descriptionEn = $descriptionEn; return $this; }
    public function getDescriptionAr(): ?string { return $this->descriptionAr; }
    public function setDescriptionAr(?string $descriptionAr): self { $this->descriptionAr = $descriptionAr; return $this; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): self { $this->imageUrl = $imageUrl; return $this; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): self { $this->position = $position; return $this; }
    public function getProducts(): Collection { return $this->products; }
}
