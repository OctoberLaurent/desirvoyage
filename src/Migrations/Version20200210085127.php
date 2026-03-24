<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200210085127 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE postal_code postal_code VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE stays ADD serial VARCHAR(255) DEFAULT NULL, ADD created_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD created_date DATETIME DEFAULT NULL');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reservation DROP created_date');
        $this->addSql('ALTER TABLE stays DROP serial, DROP created_date');
        $this->addSql('ALTER TABLE user CHANGE postal_code postal_code VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
