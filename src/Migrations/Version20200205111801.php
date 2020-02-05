<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205111801 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE traveler (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, lastname VARCHAR(80) NOT NULL, firstname VARCHAR(80) NOT NULL, email VARCHAR(50) NOT NULL, birthday DATETIME NOT NULL, INDEX IDX_6841F216B83297E7 (reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, serial VARCHAR(20) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_options (reservation_id INT NOT NULL, options_id INT NOT NULL, INDEX IDX_B7A04102B83297E7 (reservation_id), INDEX IDX_B7A041023ADB05F1 (options_id), PRIMARY KEY(reservation_id, options_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traveler ADD CONSTRAINT FK_6841F216B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation_options ADD CONSTRAINT FK_B7A04102B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_options ADD CONSTRAINT FK_B7A041023ADB05F1 FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stays ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E7B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_E2E919E7B83297E7 ON stays (reservation_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE traveler DROP FOREIGN KEY FK_6841F216B83297E7');
        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E7B83297E7');
        $this->addSql('ALTER TABLE reservation_options DROP FOREIGN KEY FK_B7A04102B83297E7');
        $this->addSql('DROP TABLE traveler');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_options');
        $this->addSql('DROP INDEX IDX_E2E919E7B83297E7 ON stays');
        $this->addSql('ALTER TABLE stays DROP reservation_id');
    }
}
