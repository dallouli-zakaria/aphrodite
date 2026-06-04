<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync persistent cart index names with Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE carts RENAME INDEX uniq_4e004a51a76ed395 TO UNIQ_4E004AACA76ED395');
        $this->addSql('ALTER TABLE cart_items RENAME INDEX idx_6a32b3a21ad5cdbf TO IDX_BEF484451AD5CDBF');
        $this->addSql('ALTER TABLE cart_items RENAME INDEX idx_6a32b3a24584665a TO IDX_BEF484454584665A');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE carts RENAME INDEX UNIQ_4E004AACA76ED395 TO uniq_4e004a51a76ed395');
        $this->addSql('ALTER TABLE cart_items RENAME INDEX IDX_BEF484451AD5CDBF TO idx_6a32b3a21ad5cdbf');
        $this->addSql('ALTER TABLE cart_items RENAME INDEX IDX_BEF484454584665A TO idx_6a32b3a24584665a');
    }
}
