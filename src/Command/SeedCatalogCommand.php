<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Subcategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed-catalog', description: 'Create starter categories, subcategories and products for AphroditeShop.')]
final class SeedCatalogCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categories = [
            'hommes' => [
                'nameFr' => 'Hommes',
                'nameEn' => 'Men',
                'descriptionFr' => 'Vetements, chaussures, accessoires et parfums homme.',
                'descriptionEn' => 'Men clothing, shoes, accessories and perfumes.',
                'imageUrl' => 'https://images.unsplash.com/photo-1516826957135-700dedea698c?auto=format&fit=crop&w=1200&q=82',
            ],
            'femmes' => [
                'nameFr' => 'Femmes',
                'nameEn' => 'Women',
                'descriptionFr' => 'Robes, tops, vestes, chaussures, accessoires et parfums femme.',
                'descriptionEn' => 'Dresses, tops, jackets, shoes, accessories and perfumes.',
                'imageUrl' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=1200&q=82',
            ],
        ];

        $categoryEntities = [];
        foreach ($categories as $slug => $data) {
            $category = $this->entityManager->getRepository(Category::class)->findOneBy(['slug' => $slug]) ?? new Category();
            $category
                ->setSlug($slug)
                ->setNameFr($data['nameFr'])
                ->setNameEn($data['nameEn'])
                ->setNameAr($data['nameFr'])
                ->setDescriptionFr($data['descriptionFr'])
                ->setDescriptionEn($data['descriptionEn'])
                ->setDescriptionAr($data['descriptionFr'])
                ->setImageUrl($data['imageUrl']);
            $this->entityManager->persist($category);
            $categoryEntities[$slug] = $category;
        }

        $subcategories = [
            ['hommes', 'nouveautes', 'Nouveautes', 'New In', 'Les nouvelles pieces homme.', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 't-shirts', 'T-shirts et polos', 'T-shirts and polos', 'Basics, coupes boxy et pieces faciles.', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'sweats', 'Sweats et hoodies', 'Sweats and hoodies', 'Volumes street et molleton confortable.', 'https://images.unsplash.com/photo-1516826957135-700dedea698c?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'pantalons', 'Pantalons et cargos', 'Pants and cargos', 'Cargos, jeans et coupes relax.', 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'chaussures', 'Chaussures', 'Shoes', 'Sneakers et paires faciles au quotidien.', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'accessoires', 'Accessoires', 'Accessories', 'Casquettes, sacs et details utiles.', 'https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'parfums', 'Parfums', 'Perfumes', 'Senteurs fraiches et boisees.', 'https://images.unsplash.com/photo-1590736704728-f4730bb30770?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'nouveautes', 'Nouveautes', 'New In', 'Les nouvelles pieces femme.', 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'robes', 'Robes et combinaisons', 'Dresses and jumpsuits', 'Pieces simples a porter et a accessoiriser.', 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'tops', 'Tops et bodies', 'Tops and bodies', 'Tops, bodies et essentiels modernes.', 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'vestes', 'Vestes', 'Jackets', 'Denim, blousons et couches legeres.', 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'chaussures', 'Chaussures', 'Shoes', 'Sneakers et silhouettes faciles.', 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'sacs', 'Sacs', 'Bags', 'Totes, petits sacs et formats pratiques.', 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'bijoux', 'Bijoux', 'Jewelry', 'Chaines, details metal et finitions.', 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'parfums', 'Parfums', 'Perfumes', 'Senteurs ambrees, propres et feminines.', 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80'],
        ];

        $subcategoryEntities = [];
        foreach ($subcategories as $position => [$categorySlug, $slug, $nameFr, $nameEn, $descriptionFr, $imageUrl]) {
            $category = $categoryEntities[$categorySlug];
            $subcategory = $this->entityManager->getRepository(Subcategory::class)->findOneBy(['category' => $category, 'slug' => $slug]) ?? new Subcategory();
            $subcategory
                ->setCategory($category)
                ->setSlug($slug)
                ->setNameFr($nameFr)
                ->setNameEn($nameEn)
                ->setNameAr($nameFr)
                ->setDescriptionFr($descriptionFr)
                ->setDescriptionEn($descriptionFr)
                ->setDescriptionAr($descriptionFr)
                ->setImageUrl($imageUrl)
                ->setPosition($position);
            $this->entityManager->persist($subcategory);
            $subcategoryEntities[$categorySlug.'/'.$slug] = $subcategory;
        }

        $products = [
            ['hommes', 't-shirts', 'T-shirt Heavy Fit', 'Aphrodite Basics', 'Coupe boxy, coton epais et tombant propre.', 189, null, 'New', null, 4.8, ['S', 'M', 'L', 'XL'], 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'sweats', 'Hoodie Washed', 'Studio Atlas', 'Molleton doux, effet lave et volume street.', 319, 399, '-20%', 'sale', 4.7, ['M', 'L', 'XL'], 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'pantalons', 'Cargo Relaxed', 'Studio Atlas', 'Volume confortable, poches nettes et style quotidien.', 379, null, 'New', null, 4.9, ['M', 'L', 'XL'], 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'chaussures', 'Runner Everyday', 'North Step', 'Sneaker minimaliste pour looks casual et street.', 459, null, 'Best', null, 4.8, ['40', '41', '42', '43', '44'], 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'accessoires', 'Casquette Canvas', 'Aphrodite Goods', 'Accessoire facile pour completer un look minimal.', 179, null, 'New', null, 4.6, ['One'], 'https://images.unsplash.com/photo-1523398002811-999ca8dec234?auto=format&fit=crop&w=900&q=80'],
            ['hommes', 'parfums', 'Citrus Blanc', 'Aphrodite Parfums', 'Notes propres, citronnees et legerement musquees.', 259, null, 'Fresh', null, 4.5, ['50ml'], 'https://images.unsplash.com/photo-1590736704728-f4730bb30770?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'vestes', 'Veste Denim Cropped', 'Maison Aphrodite', 'Denim structure et silhouette courte facile a porter.', 429, null, 'New', null, 4.9, ['S', 'M', 'L'], 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'robes', 'Robe Knit Minimal', 'Studio Muse', 'Maille douce, ligne nette et look simple a accessoiriser.', 349, null, 'Best', null, 4.6, ['S', 'M', 'L'], 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'sacs', 'Tote Structure', 'Aphrodite Goods', 'Sac spacieux avec une finition minimaliste premium.', 249, null, 'New', null, 4.8, ['One'], 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'bijoux', 'Set Chain Layer', 'Aphrodite Metals', 'Colliers superposes pour une finition subtile.', 169, null, 'Gift', null, 4.7, ['One'], 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'parfums', 'Amber Noir', 'Aphrodite Parfums', 'Ambre, vanille et bois doux pour le soir.', 299, null, 'New', null, 4.9, ['50ml', '100ml'], 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80'],
            ['femmes', 'chaussures', 'Court Retro', 'North Step', 'Silhouette retro avec semelle confortable.', 509, 599, '-15%', 'sale', 4.7, ['39', '40', '41', '42'], 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=900&q=80'],
        ];

        foreach ($products as [$categorySlug, $subcategorySlug, $nameFr, $brand, $descriptionFr, $price, $compareAt, $badge, $badgeClass, $rating, $sizes, $imageUrl]) {
            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['nameFr' => $nameFr]) ?? new Product();
            $product
                ->setCategory($categoryEntities[$categorySlug])
                ->setSubcategory($subcategoryEntities[$categorySlug.'/'.$subcategorySlug])
                ->setNameFr($nameFr)
                ->setNameEn($nameFr)
                ->setNameAr($nameFr)
                ->setBrand($brand)
                ->setDescriptionFr($descriptionFr)
                ->setDescriptionEn($descriptionFr)
                ->setDescriptionAr($descriptionFr)
                ->setPrice($price)
                ->setCompareAt($compareAt)
                ->setBadge($badge)
                ->setBadgeClass($badgeClass)
                ->setRating($rating)
                ->setSizes($sizes)
                ->setImageUrl($imageUrl)
                ->setActive(true);
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
        $io->success('Catalogue seeded: categories, subcategories and products are ready.');

        return Command::SUCCESS;
    }
}
