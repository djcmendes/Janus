<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add acl_rules table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE acl_rules (
  id CHAR(36) NOT NULL,
  subject_type VARCHAR(50) NOT NULL,
  subject_id VARCHAR(255) NOT NULL,
  role_id CHAR(36) NOT NULL,
  permission VARCHAR(50) NOT NULL,
  PRIMARY KEY (id),
  KEY IDX_ACL_SUBJECT (subject_type, subject_id),
  KEY IDX_ACL_ROLE (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE acl_rules");
    }
}
