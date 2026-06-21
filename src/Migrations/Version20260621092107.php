<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260621092107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, firstname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, send_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_4C62E638E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE formality (id INT AUTO_INCREMENT NOT NULL, destination VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE options (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, pay_at DATETIME DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, payment_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, travel_id INT DEFAULT NULL, INDEX IDX_8F7C2FC0ECAB15B3 (travel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT `FK_8F7C2FC0ECAB15B3` FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, serial VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price DOUBLE PRECISION NOT NULL, created_date DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, user_id INT NOT NULL, payment_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_42C84955D374C9DC (serial), UNIQUE INDEX UNIQ_42C849554C3A3BB (payment_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT `FK_42C849554C3A3BB` FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT `FK_42C84955A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE reservation_options (reservation_id INT NOT NULL, options_id INT NOT NULL, INDEX IDX_B7A04102B83297E7 (reservation_id), INDEX IDX_B7A041023ADB05F1 (options_id), PRIMARY KEY (reservation_id, options_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation_options ADD CONSTRAINT `FK_B7A041023ADB05F1` FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_options ADD CONSTRAINT `FK_B7A04102B83297E7` FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE reservation_stays (reservation_id INT NOT NULL, stays_id INT NOT NULL, INDEX IDX_A196C940883AF033 (stays_id), INDEX IDX_A196C940B83297E7 (reservation_id), PRIMARY KEY (reservation_id, stays_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation_stays ADD CONSTRAINT `FK_A196C940883AF033` FOREIGN KEY (stays_id) REFERENCES stays (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_stays ADD CONSTRAINT `FK_A196C940B83297E7` FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE stays (id INT AUTO_INCREMENT NOT NULL, star_date DATETIME NOT NULL, end_date DATETIME NOT NULL, depature VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, arrival VARCHAR(60) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, serial VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_date DATETIME DEFAULT NULL, travel_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E2E919E7D374C9DC (serial), INDEX IDX_E2E919E7ECAB15B3 (travel_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE stays ADD CONSTRAINT `FK_E2E919E7ECAB15B3` FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE travel (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, subtitle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, descriptions LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, categories_id INT DEFAULT NULL, INDEX IDX_2D0B6BCEA21214B7 (categories_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT `FK_2D0B6BCEA21214B7` FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE traveler (id INT AUTO_INCREMENT NOT NULL, lastname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, firstname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, birthday DATETIME NOT NULL, reservation_id INT DEFAULT NULL, INDEX IDX_6841F216B83297E7 (reservation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE traveler ADD CONSTRAINT `FK_6841F216B83297E7` FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE travel_formality (travel_id INT NOT NULL, formality_id INT NOT NULL, INDEX IDX_F9D4D4EEECAB15B3 (travel_id), INDEX IDX_F9D4D4EEDCCF3332 (formality_id), PRIMARY KEY (travel_id, formality_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE travel_formality ADD CONSTRAINT `FK_F9D4D4EEDCCF3332` FOREIGN KEY (formality_id) REFERENCES formality (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE travel_formality ADD CONSTRAINT `FK_F9D4D4EEECAB15B3` FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE travel_options (travel_id INT NOT NULL, options_id INT NOT NULL, INDEX IDX_11764D96ECAB15B3 (travel_id), INDEX IDX_11764D963ADB05F1 (options_id), PRIMARY KEY (travel_id, options_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE travel_options ADD CONSTRAINT `FK_11764D963ADB05F1` FOREIGN KEY (options_id) REFERENCES options (id)');
        $this->addSql('ALTER TABLE travel_options ADD CONSTRAINT `FK_11764D96ECAB15B3` FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, firstname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lastname VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, enabled TINYINT NOT NULL, token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, token_expire DATETIME DEFAULT NULL, address VARCHAR(90) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, additional_address VARCHAR(80) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, country VARCHAR(80) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, postal_code VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, birthday DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `categories`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `contact`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `formality`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `options`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `payment`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `pictures`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `reservation`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `reservation_options`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `reservation_stays`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `stays`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `travel`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `traveler`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `travel_formality`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `travel_options`');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDB1052Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDB1052Platform'."
        );

        $this->addSql('DROP TABLE `user`');
    }
}
