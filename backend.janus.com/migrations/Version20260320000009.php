<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create revisions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `revisions` (
                `id`          BINARY(16)   NOT NULL,
                `collection`  VARCHAR(200) NOT NULL,
                `item`        VARCHAR(255) NOT NULL,
                `data`        JSON         NOT NULL,
                `delta`       JSON         DEFAULT NULL,
                `version`     INT          NOT NULL DEFAULT 1,
                `activity_id` VARCHAR(36)  DEFAULT NULL,
                `created_at`  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                KEY `IDX_REVISIONS_COLLECTION_ITEM` (`collection`, `item`),
                KEY `IDX_REVISIONS_ITEM_VERSION`    (`collection`, `item`, `version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `revisions`');
    }
}
