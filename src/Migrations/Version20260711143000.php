<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260711143000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store every monetary amount as integer cents instead of floating-point euros.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE stays SET price = ROUND(price * 100)');
        $this->addSql('ALTER TABLE stays MODIFY price INT NOT NULL');
        $this->addSql('UPDATE options SET price = ROUND(price * 100)');
        $this->addSql('ALTER TABLE options MODIFY price INT NOT NULL');
        $this->addSql('UPDATE reservation SET price = ROUND(price * 100)');
        $this->addSql('ALTER TABLE reservation MODIFY price INT NOT NULL');
        $this->addSql('UPDATE payment SET amount = ROUND(amount * 100) WHERE amount IS NOT NULL');
        $this->addSql('ALTER TABLE payment MODIFY amount INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE stays MODIFY price DOUBLE PRECISION NOT NULL');
        $this->addSql('UPDATE stays SET price = price / 100');
        $this->addSql('ALTER TABLE options MODIFY price DOUBLE PRECISION NOT NULL');
        $this->addSql('UPDATE options SET price = price / 100');
        $this->addSql('ALTER TABLE reservation MODIFY price DOUBLE PRECISION NOT NULL');
        $this->addSql('UPDATE reservation SET price = price / 100');
        $this->addSql('ALTER TABLE payment MODIFY amount DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('UPDATE payment SET amount = amount / 100 WHERE amount IS NOT NULL');
    }
}
