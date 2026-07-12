<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711164500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align the Stripe payment unique index name with Doctrine metadata.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment RENAME INDEX UNIQ_PAYMENT_EXTERNAL_ID TO UNIQ_6D28840D4C3A3BB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE payment RENAME INDEX UNIQ_6D28840D4C3A3BB TO UNIQ_PAYMENT_EXTERNAL_ID');
    }
}
