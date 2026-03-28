<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Performance indexes for the Portals module.
 *
 * pages table:
 *   idx_pages_sort_order  — speeds up tree ordering queries (ORDER BY sort_order)
 *   idx_pages_full_path   — speeds up path-based lookups used by consumers
 *
 * magnets table:
 *   idx_magnets_portal_status  — speeds up "active magnets per portal" count
 *   idx_magnets_status_sched   — speeds up scheduler query (active + schedule IS NOT NULL)
 *
 * magnet_runs table:
 *   idx_runs_magnet_started    — speeds up run history (ORDER BY started_at DESC)
 *
 * acl_rules table:
 *   idx_acl_subject            — speeds up findBySubject() used by AclVoter on every request
 *
 * Note: (portal_id, full_path) unique index and tree indexes were added in migration 000033.
 */
final class Version20260328000034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add performance indexes for portals module queries';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_pages_sort_order  ON pages (parent_id, sort_order)');
        $this->addSql('CREATE INDEX idx_pages_full_path   ON pages (full_path)');

        $this->addSql('CREATE INDEX idx_magnets_portal_status ON magnets (portal_id, status)');
        $this->addSql('CREATE INDEX idx_magnets_status_sched  ON magnets (status, schedule)');

        $this->addSql('CREATE INDEX idx_runs_magnet_started ON magnet_runs (magnet_id, started_at DESC)');

        $this->addSql('CREATE INDEX idx_acl_subject ON acl_rules (subject_type, subject_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_pages_sort_order  ON pages');
        $this->addSql('DROP INDEX idx_pages_full_path   ON pages');
        $this->addSql('DROP INDEX idx_magnets_portal_status ON magnets');
        $this->addSql('DROP INDEX idx_magnets_status_sched  ON magnets');
        $this->addSql('DROP INDEX idx_runs_magnet_started ON magnet_runs');
        $this->addSql('DROP INDEX idx_acl_subject ON acl_rules');
    }
}
