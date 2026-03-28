<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add portals table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE portals (
  id CHAR(36) NOT NULL,
  name VARCHAR(255) NOT NULL,
  base_route VARCHAR(255) NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'draft',
  settings_json JSON DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE portals");
    }
}
