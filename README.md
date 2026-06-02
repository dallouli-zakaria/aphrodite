# AphroditeShop

A Symfony/Twig ecommerce storefront for AphroditeShop.

The project still keeps `index.html` and `aphroditeShop.html` at the repository root for the static GitHub Pages preview, but the real backend application now starts from Symfony's `public/index.php`.

## Pages

- Symfony storefront: `/`
- Admin shell: `/admin`
- Static preview home: `#/`
- Static preview Hommes: `#/men`
- Static preview Femmes: `#/women`
- Static preview Accessoires: `#/accessories`
- Static preview Parfums: `#/perfumes`
- Static preview Sneakers: `#/sneakers`
- Static preview New In: `#/new-in`
- Static preview Checkout: `#/checkout`

## Local Symfony Development

Install dependencies:

```bash
composer install
```

Create the MySQL database:

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
```

Run locally with PHP's built-in server:

```bash
php -S 127.0.0.1:8000 -t public
```

Then open:

```text
http://127.0.0.1:8000/
http://127.0.0.1:8000/admin
```

Useful checks:

```bash
php bin/console lint:twig Templates
php bin/console debug:router
composer audit
```

## Symfony Stack

- Symfony 7.4
- Twig with the `Templates/` convention used in TalDar
- Doctrine ORM and migrations
- MySQL database by default: `aphrodite_shop`
- Symfony Security
- EasyAdmin shell for the future admin dashboard
- Symfony AssetMapper, Stimulus, and UX Turbo

## Database

The default development connection is:

```text
DATABASE_URL="mysql://root:@127.0.0.1:3306/aphrodite_shop?serverVersion=8.0.32&charset=utf8mb4"
```

If your local MySQL uses a password, change this value in `.env.local` instead of editing `.env`.

## Publish Static Preview With GitHub Pages

1. Create a new public GitHub repository, for example `aphrodite-shop`.
2. Upload `index.html` and `README.md`.
3. Open repository `Settings`.
4. Go to `Pages`.
5. Under `Build and deployment`, choose `Deploy from a branch`.
6. Select branch `main` and folder `/root`.
7. Save.

The website URL will look like:

```text
https://your-github-username.github.io/aphrodite-shop/
```

## Notes

The static preview uses external image URLs, so it should be viewed as a normal webpage. Gmail is not a good host for this because email clients often block JavaScript, remove parts of CSS, and may hide remote images.

For production ecommerce hosting, point the web server document root to `public/`, not the repository root.
