<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Subcategory;
use Doctrine\ORM\EntityManagerInterface;

class CatalogService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findCategory(string $slug): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['slug' => $slug]);
    }

    public function findSubcategory(Category $category, string $slug): ?Subcategory
    {
        return $this->entityManager->getRepository(Subcategory::class)->findOneBy([
            'category' => $category,
            'slug' => $slug,
        ]);
    }

    /**
     * @return Category[]
     */
    public function findCategories(): array
    {
        return $this->entityManager->getRepository(Category::class)->findBy([], ['id' => 'ASC']);
    }

    /**
     * @return Product[]
     */
    public function findProducts(?Category $category = null, ?Subcategory $subcategory = null, ?int $limit = null): array
    {
        $builder = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('product')
            ->leftJoin('product.category', 'category')->addSelect('category')
            ->leftJoin('product.subcategory', 'subcategory')->addSelect('subcategory')
            ->andWhere('product.active = :active')
            ->setParameter('active', true)
            ->orderBy('product.createdAt', 'DESC')
            ->addOrderBy('product.id', 'DESC');

        if ($category) {
            $builder->andWhere('product.category = :category')->setParameter('category', $category);
        }

        if ($subcategory) {
            $builder->andWhere('product.subcategory = :subcategory')->setParameter('subcategory', $subcategory);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @return Product[]
     */
    public function findSimilarProducts(Product $product, int $limit): array
    {
        $builder = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('product')
            ->leftJoin('product.category', 'category')->addSelect('category')
            ->leftJoin('product.subcategory', 'subcategory')->addSelect('subcategory')
            ->andWhere('product.active = :active')
            ->andWhere('product.id != :productId')
            ->setParameter('active', true)
            ->setParameter('productId', $product->getId())
            ->setMaxResults($limit)
            ->orderBy('product.createdAt', 'DESC')
            ->addOrderBy('product.id', 'DESC');

        if ($product->getSubcategory()) {
            $builder
                ->andWhere('product.subcategory = :subcategory')
                ->setParameter('subcategory', $product->getSubcategory());
        } elseif ($product->getCategory()) {
            $builder
                ->andWhere('product.category = :category')
                ->setParameter('category', $product->getCategory());
        }

        $similarProducts = $builder->getQuery()->getResult();
        if (count($similarProducts) >= $limit || !$product->getCategory()) {
            return $similarProducts;
        }

        $fallback = $this->entityManager->getRepository(Product::class)
            ->createQueryBuilder('product')
            ->leftJoin('product.category', 'category')->addSelect('category')
            ->leftJoin('product.subcategory', 'subcategory')->addSelect('subcategory')
            ->andWhere('product.active = :active')
            ->andWhere('product.id != :productId')
            ->andWhere('product.category = :category')
            ->setParameter('active', true)
            ->setParameter('productId', $product->getId())
            ->setParameter('category', $product->getCategory())
            ->setMaxResults($limit)
            ->orderBy('product.createdAt', 'DESC')
            ->addOrderBy('product.id', 'DESC')
            ->getQuery()
            ->getResult();

        $indexed = [];
        foreach ([...$similarProducts, ...$fallback] as $item) {
            $indexed[$item->getId()] = $item;
        }

        return array_slice(array_values($indexed), 0, $limit);
    }

    public function slugify(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($value)) ?? '';
        $value = trim($value, '-');

        return $value ?: 'produit';
    }
}
