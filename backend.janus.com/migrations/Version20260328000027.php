<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add components table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE components (
  id CHAR(36) NOT NULL,
  type VARCHAR(100) NOT NULL,
  collection_id VARCHAR(255) DEFAULT NULL,
  query_config_json JSON DEFAULT NULL,
  render_config_json JSON DEFAULT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE components");
    }
}
