<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205132152 extends AbstractMigration
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

        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E7B83297E7');
        $this->addSql('DROP INDEX IDX_E2E919E7B83297E7 ON stays');
        $this->addSql('ALTER TABLE stays DROP reservation_id');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stays ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E7B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E2E919E7B83297E7 ON stays (reservation_id)');
    }
}
