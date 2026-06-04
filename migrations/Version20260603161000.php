<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603161000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store selected product size on cart items';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_items ADD size VARCHAR(40) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE cart_items DROP INDEX UNIQ_CART_PRODUCT');
        $this->addSql('ALTER TABLE cart_items ADD UNIQUE INDEX UNIQ_CART_PRODUCT_SIZE (cart_id, product_id, size)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_items DROP INDEX UNIQ_CART_PRODUCT_SIZE');
        $this->addSql('ALTER TABLE cart_items DROP size');
        $this->addSql('ALTER TABLE cart_items ADD UNIQUE INDEX UNIQ_CART_PRODUCT (cart_id, product_id)');
    }
}
