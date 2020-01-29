<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200129084054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE travel_options (travel_id INT NOT NULL, options_id INT NOT NULL, INDEX IDX_11764D96ECAB15B3 (travel_id), INDEX IDX_11764D963ADB05F1 (options_id), PRIMARY KEY(travel_id, options_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE travel_options ADD CONSTRAINT FK_11764D96ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE travel_options ADD CONSTRAINT FK_11764D963ADB05F1 FOREIGN KEY (options_id) REFERENCES options (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE options DROP FOREIGN KEY FK_D035FA87ECAB15B3');
        $this->addSql('DROP INDEX IDX_D035FA87ECAB15B3 ON options');
        $this->addSql('ALTER TABLE options DROP travel_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE travel_options');
        $this->addSql('ALTER TABLE options ADD travel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE options ADD CONSTRAINT FK_D035FA87ECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D035FA87ECAB15B3 ON options (travel_id)');
    }
}
