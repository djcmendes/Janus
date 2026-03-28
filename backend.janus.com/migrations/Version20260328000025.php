<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add modules table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE modules (
  id CHAR(36) NOT NULL,
  type VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  config_json JSON DEFAULT NULL,
  portal_id CHAR(36) DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IDX_MODULES_PORTAL (portal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE modules");
    }
}
