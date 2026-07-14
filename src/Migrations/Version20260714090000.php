<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260714090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize stored image paths to public data URLs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE pictures SET url = CONCAT('/data/', SUBSTRING_INDEX(url, '/', -1)) WHERE url IS NOT NULL AND url <> '' AND url NOT LIKE '/data/%' AND (url LIKE '%/public/data/%' OR url LIKE '%/public/data2/%' OR url LIKE 'data/%' OR url LIKE 'data2/%')");
        $this->addSql("UPDATE categories SET url = CONCAT('/data/', SUBSTRING_INDEX(url, '/', -1)) WHERE url IS NOT NULL AND url <> '' AND url NOT LIKE '/data/%' AND (url LIKE '%/public/data/%' OR url LIKE '%/public/data2/%' OR url LIKE 'data/%' OR url LIKE 'data2/%')");
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Normalized image paths cannot be restored to their original filesystem paths.');
    }
}
