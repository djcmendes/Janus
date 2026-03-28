<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add module_placements table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE module_placements (
  id CHAR(36) NOT NULL,
  page_id CHAR(36) NOT NULL,
  position_name VARCHAR(100) NOT NULL,
  module_id CHAR(36) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY IDX_MP_PAGE (page_id),
  KEY IDX_MP_MODULE (module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE module_placements");
    }
}
