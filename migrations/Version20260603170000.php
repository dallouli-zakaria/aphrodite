<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260603170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add customer order history and shipping process fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders ADD user_id INT DEFAULT NULL, ADD order_number VARCHAR(40) DEFAULT NULL, ADD shipping_status VARCHAR(40) DEFAULT \'pending_confirmation\' NOT NULL, ADD shipping_method VARCHAR(80) DEFAULT \'Livraison standard\' NOT NULL, ADD tracking_number VARCHAR(120) DEFAULT NULL, ADD estimated_delivery VARCHAR(120) DEFAULT \'2 a 5 jours ouvrables\' NOT NULL, ADD shipping_notes LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E52FFDEE1EF099D0 ON orders (order_number)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE order_items ADD size VARCHAR(40) DEFAULT \'\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('DROP INDEX IDX_E52FFDEEA76ED395 ON orders');
        $this->addSql('DROP INDEX UNIQ_E52FFDEE1EF099D0 ON orders');
        $this->addSql('ALTER TABLE orders DROP user_id, DROP order_number, DROP shipping_status, DROP shipping_method, DROP tracking_number, DROP estimated_delivery, DROP shipping_notes');
        $this->addSql('ALTER TABLE order_items DROP size');
    }
}
