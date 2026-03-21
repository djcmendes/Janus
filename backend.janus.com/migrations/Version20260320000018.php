<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create versions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE versions (
                id           BINARY(16)   NOT NULL,
                collection   VARCHAR(64)  NOT NULL,
                item         VARCHAR(36)  NOT NULL,
                version_key  VARCHAR(64)  NOT NULL,
                data         LONGTEXT     NOT NULL COMMENT '(DC2Type:json)',
                delta        LONGTEXT     DEFAULT NULL COMMENT '(DC2Type:json)',
                user_id      VARCHAR(36)  DEFAULT NULL,
                created_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at   DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                UNIQUE KEY uniq_version_collection_item_key (collection, item, version_key),
                KEY idx_version_collection_item (collection, item)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE versions');
    }
}
