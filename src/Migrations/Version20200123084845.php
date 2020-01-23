<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123084845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE formality (id INT AUTO_INCREMENT NOT NULL, destination VARCHAR(60) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE travel_formality (travel_id INT NOT NULL, formality_id INT NOT NULL, INDEX IDX_F9D4D4EEECAB15B3 (travel_id), INDEX IDX_F9D4D4EEDCCF3332 (formality_id), PRIMARY KEY(travel_id, formality_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE options (id INT AUTO_INCREMENT NOT NULL, travel_id INT DEFAULT NULL, name VARCHAR(60) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(60) NOT NULL, INDEX IDX_D035FA87ECAB15B3 (travel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel_formality ADD CONSTRAINT FK_F9D4D4EEECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE travel_formality ADD CONSTRAINT FK_F9D4D4EEDCCF3332 FOREIGN KEY (formality_id) REFERENCES formality (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE options ADD CONSTRAINT FK_D035FA87ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE travel_formality DROP FOREIGN KEY FK_F9D4D4EEDCCF3332');
        $this->addSql('DROP TABLE formality');
        $this->addSql('DROP TABLE travel_formality');
        $this->addSql('DROP TABLE options');
    }
}
