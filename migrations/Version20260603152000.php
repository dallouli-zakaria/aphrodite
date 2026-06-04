<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603152000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add persistent customer carts';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE carts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4E004A51A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6A32B3A21AD5CDBF (cart_id), INDEX IDX_6A32B3A24584665A (product_id), UNIQUE INDEX UNIQ_CART_PRODUCT (cart_id, product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004A51A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_6A32B3A21AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_6A32B3A24584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_6A32B3A21AD5CDBF');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_6A32B3A24584665A');
        $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004A51A76ED395');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE carts');
    }
}
