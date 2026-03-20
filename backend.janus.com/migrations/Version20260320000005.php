<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create janus_collections and janus_fields metadata tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `janus_collections` (
                `id`         BINARY(16)   NOT NULL,
                `name`       VARCHAR(64)  NOT NULL,
                `label`      VARCHAR(255) DEFAULT NULL,
                `icon`       VARCHAR(50)  DEFAULT NULL,
                `note`       LONGTEXT     DEFAULT NULL,
                `hidden`     TINYINT(1)   NOT NULL DEFAULT 0,
                `singleton`  TINYINT(1)   NOT NULL DEFAULT 0,
                `sort_field` VARCHAR(64)  DEFAULT NULL,
                `created_at` DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at` DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_COLLECTION_NAME` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE `janus_fields` (
                `id`         BINARY(16)  NOT NULL,
                `collection` VARCHAR(64) NOT NULL,
                `field`      VARCHAR(64) NOT NULL,
                `type`       VARCHAR(30) NOT NULL,
                `label`      VARCHAR(255) DEFAULT NULL,
                `note`       LONGTEXT    DEFAULT NULL,
                `required`   TINYINT(1)  NOT NULL DEFAULT 0,
                `readonly`   TINYINT(1)  NOT NULL DEFAULT 0,
                `hidden`     TINYINT(1)  NOT NULL DEFAULT 0,
                `sort_order` INT         NOT NULL DEFAULT 0,
                `created_at` DATETIME    NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at` DATETIME    DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_FIELD_COLLECTION_FIELD` (`collection`, `field`),
                KEY `IDX_FIELDS_COLLECTION` (`collection`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `janus_fields`');
        $this->addSql('DROP TABLE `janus_collections`');
    }
}
