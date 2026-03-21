<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create janus_relations metadata table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `janus_relations` (
                `id`                  BINARY(16)  NOT NULL,
                `many_collection`     VARCHAR(64) NOT NULL,
                `many_field`          VARCHAR(64) NOT NULL,
                `one_collection`      VARCHAR(64) DEFAULT NULL,
                `one_field`           VARCHAR(64) DEFAULT NULL,
                `junction_collection` VARCHAR(64) DEFAULT NULL,
                `created_at`          DATETIME    NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                `updated_at`          DATETIME    DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`),
                UNIQUE KEY `UNIQ_RELATION_COLLECTION_FIELD` (`many_collection`, `many_field`),
                KEY `IDX_RELATIONS_MANY_COLLECTION` (`many_collection`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `janus_relations`');
    }
}
