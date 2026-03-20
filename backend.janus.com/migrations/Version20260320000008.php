<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create activity table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `activity` (
                `id`         BINARY(16)   NOT NULL,
                `action`     VARCHAR(50)  NOT NULL,
                `collection` VARCHAR(200) DEFAULT NULL,
                `item`       VARCHAR(255) DEFAULT NULL,
                `user_id`    VARCHAR(255) DEFAULT NULL,
                `ip`         VARCHAR(255) DEFAULT NULL,
                `user_agent` VARCHAR(255) DEFAULT NULL,
                `timestamp`  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                KEY `IDX_ACTIVITY_TIMESTAMP` (`timestamp`),
                KEY `IDX_ACTIVITY_COLLECTION` (`collection`),
                KEY `IDX_ACTIVITY_USER` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `activity`');
    }
}
