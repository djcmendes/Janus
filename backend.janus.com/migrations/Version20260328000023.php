<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add layout_templates table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE layout_templates (
  id CHAR(36) NOT NULL,
  name VARCHAR(255) NOT NULL,
  positions_json JSON NOT NULL,
  template_markup LONGTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE layout_templates");
    }
}
