<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602133000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add catalogue subcategories and connect products to them';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE subcategories (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, slug VARCHAR(80) NOT NULL, name_fr VARCHAR(120) NOT NULL, name_en VARCHAR(120) NOT NULL, name_ar VARCHAR(120) DEFAULT NULL, description_fr LONGTEXT DEFAULT NULL, description_en LONGTEXT DEFAULT NULL, description_ar LONGTEXT DEFAULT NULL, image_url VARCHAR(500) DEFAULT NULL, position INT DEFAULT 0 NOT NULL, INDEX IDX_1D9ADDC712469DE2 (category_id), UNIQUE INDEX uniq_subcategory_parent_slug (category_id, slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subcategories ADD CONSTRAINT FK_1D9ADDC712469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products ADD subcategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A5DC6FE57 FOREIGN KEY (subcategory_id) REFERENCES subcategories (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A5DC6FE57 ON products (subcategory_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A5DC6FE57');
        $this->addSql('ALTER TABLE subcategories DROP FOREIGN KEY FK_1D9ADDC712469DE2');
        $this->addSql('DROP INDEX IDX_B3BA5A5A5DC6FE57 ON products');
        $this->addSql('ALTER TABLE products DROP subcategory_id');
        $this->addSql('DROP TABLE subcategories');
    }
}
