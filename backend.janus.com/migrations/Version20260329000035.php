<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add portal_css column to portals table.
 *
 * The Portal entity declared this column but the original migration (000022)
 * was missing it, causing all portals queries to fail with an "Unknown column"
 * error from MariaDB.
 */
final class Version20260329000035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing portal_css column to portals table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE portals ADD COLUMN portal_css LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE portals DROP COLUMN portal_css');
    }
}
