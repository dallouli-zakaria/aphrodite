<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260602134000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Synchronize MySQL schema names with Doctrine mapping';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories RENAME INDEX uniq_3af346687989d9b TO UNIQ_3AF34668989D9B62');
        $this->addSql('ALTER TABLE customers CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE created_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE subcategories RENAME INDEX idx_1d9addc712469de2 TO IDX_6562A1CB12469DE2');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories RENAME INDEX UNIQ_3AF34668989D9B62 TO uniq_3af346687989d9b');
        $this->addSql('ALTER TABLE customers CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE orders CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE products CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE subcategories RENAME INDEX IDX_6562A1CB12469DE2 TO idx_1d9addc712469de2');
    }
}
