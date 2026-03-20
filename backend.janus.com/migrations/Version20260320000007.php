<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create folders and files tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `folders` (
                `id`         BINARY(16)   NOT NULL,
                `name`       VARCHAR(255) NOT NULL,
                `parent_id`  BINARY(16)   DEFAULT NULL,
                `created_at` DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at` DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                KEY `IDX_FOLDERS_PARENT` (`parent_id`),
                CONSTRAINT `FK_FOLDERS_PARENT`
                    FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE `files` (
                `id`                BINARY(16)   NOT NULL,
                `storage`           VARCHAR(20)  NOT NULL DEFAULT 'local',
                `filename_disk`     VARCHAR(255) NOT NULL,
                `filename_download` VARCHAR(255) NOT NULL,
                `title`             VARCHAR(255) DEFAULT NULL,
                `type`              VARCHAR(100) NOT NULL,
                `filesize`          BIGINT       DEFAULT NULL,
                `width`             INT          DEFAULT NULL,
                `height`            INT          DEFAULT NULL,
                `uploaded_by`       VARCHAR(36)  DEFAULT NULL,
                `folder_id`         BINARY(16)   DEFAULT NULL,
                `created_at`        DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`        DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                KEY `IDX_FILES_FOLDER` (`folder_id`),
                CONSTRAINT `FK_FILES_FOLDER`
                    FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `files`');
        $this->addSql('DROP TABLE `folders`');
    }
}
