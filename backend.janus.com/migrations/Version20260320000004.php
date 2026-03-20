<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create policies, permissions, and access tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `policies` (
                `id`           BINARY(16)   NOT NULL,
                `name`         VARCHAR(100) NOT NULL,
                `description`  LONGTEXT     DEFAULT NULL,
                `icon`         VARCHAR(50)  DEFAULT NULL,
                `enforce_tfa`  TINYINT(1)   NOT NULL DEFAULT 0,
                `admin_access` TINYINT(1)   NOT NULL DEFAULT 0,
                `app_access`   TINYINT(1)   NOT NULL DEFAULT 1,
                `ip_access`    JSON         DEFAULT NULL,
                `created_at`   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`   DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_POLICY_NAME` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE `permissions` (
                `id`                  BINARY(16)   NOT NULL,
                `policy_id`           BINARY(16)   NOT NULL,
                `collection`          VARCHAR(255) DEFAULT NULL,
                `action`              VARCHAR(20)  NOT NULL,
                `fields`              JSON         DEFAULT NULL,
                `permissions_filter`  JSON         DEFAULT NULL,
                `validation`          JSON         DEFAULT NULL,
                `presets`             JSON         DEFAULT NULL,
                `created_at`          DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`          DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                KEY `IDX_PERMISSIONS_POLICY` (`policy_id`),
                CONSTRAINT `FK_PERMISSIONS_POLICY`
                    FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE `access` (
                `id`         BINARY(16) NOT NULL,
                `role_id`    BINARY(16) DEFAULT NULL,
                `policy_id`  BINARY(16) NOT NULL,
                `created_at` DATETIME   NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_ACCESS_ROLE_POLICY` (`role_id`, `policy_id`),
                KEY `IDX_ACCESS_POLICY` (`policy_id`),
                CONSTRAINT `FK_ACCESS_ROLE`
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
                CONSTRAINT `FK_ACCESS_POLICY`
                    FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `access`');
        $this->addSql('DROP TABLE `permissions`');
        $this->addSql('DROP TABLE `policies`');
    }
}
