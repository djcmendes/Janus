<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `users` (
                `id`                       BINARY(16)   NOT NULL,
                `email`                    VARCHAR(180) NOT NULL,
                `roles`                    JSON         NOT NULL,
                `password`                 VARCHAR(255) NOT NULL DEFAULT '',
                `status`                   VARCHAR(20)  NOT NULL DEFAULT 'active',
                `first_name`               VARCHAR(120) DEFAULT NULL,
                `last_name`                VARCHAR(120) DEFAULT NULL,
                `last_access_at`           DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                `invite_token`             VARCHAR(255) DEFAULT NULL,
                `invite_token_expires_at`  DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                `created_at`               DATETIME     NOT NULL   COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`               DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                `deleted_at`               DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_EMAIL` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `users`');
    }
}
