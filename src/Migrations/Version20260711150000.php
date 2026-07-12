<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add explicit reservation lifecycle status and enforce unique Stripe payment identifiers.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE reservation ADD status VARCHAR(20) DEFAULT 'pending' NOT NULL");
        $this->addSql("UPDATE reservation SET status = 'paid' WHERE payment_id IS NOT NULL");
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PAYMENT_EXTERNAL_ID ON payment (payment_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_PAYMENT_EXTERNAL_ID ON payment');
        $this->addSql('ALTER TABLE reservation DROP status');
    }
}
