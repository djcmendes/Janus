<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pages table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE pages (
  id CHAR(36) NOT NULL,
  portal_id CHAR(36) NOT NULL,
  parent_id CHAR(36) DEFAULT NULL,
  slug VARCHAR(255) NOT NULL,
  full_path VARCHAR(1024) NOT NULL,
  title VARCHAR(255) NOT NULL,
  layout_template_id CHAR(36) DEFAULT NULL,
  center_component_id CHAR(36) DEFAULT NULL,
  custom_css LONGTEXT DEFAULT NULL,
  meta_json JSON DEFAULT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'draft',
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IDX_PAGES_PORTAL (portal_id),
  KEY IDX_PAGES_PARENT (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE pages");
    }
}
