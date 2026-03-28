<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add magnets table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE magnets (
  id CHAR(36) NOT NULL,
  portal_id CHAR(36) NOT NULL,
  name VARCHAR(255) NOT NULL,
  source_type VARCHAR(100) NOT NULL,
  source_config_json JSON DEFAULT NULL,
  target_collection_id VARCHAR(255) NOT NULL,
  schedule VARCHAR(100) DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IDX_MAGNETS_PORTAL (portal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE magnets");
    }
}
