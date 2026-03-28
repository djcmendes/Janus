<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add portal_css column to portals table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE portals ADD COLUMN portal_css LONGTEXT DEFAULT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE portals DROP COLUMN portal_css");
    }
}
