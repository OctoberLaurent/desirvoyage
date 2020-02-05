<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205132506 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reservation_stays (reservation_id INT NOT NULL, stays_id INT NOT NULL, INDEX IDX_A196C940B83297E7 (reservation_id), INDEX IDX_A196C940883AF033 (stays_id), PRIMARY KEY(reservation_id, stays_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation_stays ADD CONSTRAINT FK_A196C940B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_stays ADD CONSTRAINT FK_A196C940883AF033 FOREIGN KEY (stays_id) REFERENCES stays (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD stock INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reservation_stays');
        $this->addSql('ALTER TABLE reservation DROP stock');
    }
}
