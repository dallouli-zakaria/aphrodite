<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603162000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove default value from cart item size';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_items CHANGE size size VARCHAR(40) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_items CHANGE size size VARCHAR(40) DEFAULT \'\' NOT NULL');
    }
}
