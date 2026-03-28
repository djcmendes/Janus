<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds a unique index on pages.full_path scoped to portal_id so two pages
 * in the same portal cannot share the same path.
 * Also adds a non-unique index on (portal_id, parent_id) for tree queries.
 */
final class Version20260328000033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add full_path unique index and tree query indexes on pages table';
    }

    public function up(Schema $schema): void
    {
        // Unique path per portal — prevents duplicates at the DB level
        $this->addSql('CREATE UNIQUE INDEX uniq_pages_portal_full_path ON pages (portal_id, full_path)');

        // Speeds up tree queries: "give me all children of parent X"
        $this->addSql('CREATE INDEX idx_pages_portal_parent ON pages (portal_id, parent_id)');

        // Speeds up status filters used by dashboard metrics
        $this->addSql('CREATE INDEX idx_pages_portal_status ON pages (portal_id, status)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_pages_portal_full_path ON pages');
        $this->addSql('DROP INDEX idx_pages_portal_parent ON pages');
        $this->addSql('DROP INDEX idx_pages_portal_status ON pages');
    }
}
