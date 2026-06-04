<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603171000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync order tracking defaults with Doctrine metadata';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_E52FFDEE1EF099D0 ON orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ORDER_NUMBER ON orders (order_number)');
        $this->addSql('ALTER TABLE orders CHANGE shipping_status shipping_status VARCHAR(40) NOT NULL, CHANGE shipping_method shipping_method VARCHAR(80) NOT NULL, CHANGE estimated_delivery estimated_delivery VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE order_items CHANGE size size VARCHAR(40) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_ORDER_NUMBER ON orders');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE1EF099D0 ON orders (order_number)');
        $this->addSql('ALTER TABLE orders CHANGE shipping_status shipping_status VARCHAR(40) DEFAULT \'pending_confirmation\' NOT NULL, CHANGE shipping_method shipping_method VARCHAR(80) DEFAULT \'Livraison standard\' NOT NULL, CHANGE estimated_delivery estimated_delivery VARCHAR(120) DEFAULT \'2 a 5 jours ouvrables\' NOT NULL');
        $this->addSql('ALTER TABLE order_items CHANGE size size VARCHAR(40) DEFAULT \'\' NOT NULL');
    }
}
