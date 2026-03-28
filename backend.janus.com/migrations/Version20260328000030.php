<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add magnet_runs table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE magnet_runs (
  id CHAR(36) NOT NULL,
  magnet_id CHAR(36) NOT NULL,
  started_at DATETIME NOT NULL,
  finished_at DATETIME DEFAULT NULL,
  items_imported INT NOT NULL DEFAULT 0,
  errors_json JSON DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IDX_MAGNET_RUNS_MAGNET (magnet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE magnet_runs");
    }
}
