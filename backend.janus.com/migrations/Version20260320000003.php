<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create roles table and add role_id FK to users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `roles` (
                `id`           BINARY(16)   NOT NULL,
                `name`         VARCHAR(100) NOT NULL,
                `description`  LONGTEXT     DEFAULT NULL,
                `icon`         VARCHAR(50)  DEFAULT NULL,
                `enforce_tfa`  TINYINT(1)   NOT NULL DEFAULT 0,
                `admin_access` TINYINT(1)   NOT NULL DEFAULT 0,
                `app_access`   TINYINT(1)   NOT NULL DEFAULT 1,
                `created_at`   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`   DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_ROLE_NAME` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE `users`
                ADD COLUMN `role_id` BINARY(16) DEFAULT NULL,
                ADD CONSTRAINT `FK_USERS_ROLE`
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
                    ON DELETE SET NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `users` DROP FOREIGN KEY `FK_USERS_ROLE`');
        $this->addSql('ALTER TABLE `users` DROP COLUMN `role_id`');
        $this->addSql('DROP TABLE `roles`');
    }
}
