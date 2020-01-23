<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200122161732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE travel (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subtitle VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, descriptions LONGTEXT DEFAULT NULL, INDEX IDX_2D0B6BCEA21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(80) NOT NULL, lastname VARCHAR(80) NOT NULL, enabled TINYINT(1) NOT NULL, token VARCHAR(255) DEFAULT NULL, token_expire DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stays (id INT AUTO_INCREMENT NOT NULL, travel_id INT DEFAULT NULL, star_date DATETIME NOT NULL, end_date DATETIME NOT NULL, depature VARCHAR(60) NOT NULL, arrival VARCHAR(60) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_E2E919E7ECAB15B3 (travel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, travel_id INT DEFAULT NULL, name VARCHAR(80) DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_8F7C2FC0ECAB15B3 (travel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCEA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT FK_E2E919E7ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stays DROP FOREIGN KEY FK_E2E919E7ECAB15B3');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0ECAB15B3');
        $this->addSql('ALTER TABLE travel DROP FOREIGN KEY FK_2D0B6BCEA21214B7');
        $this->addSql('DROP TABLE travel');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE stays');
        $this->addSql('DROP TABLE pictures');
        $this->addSql('DROP TABLE categories');
    }
}
