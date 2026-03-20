<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create presets table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE presets (
                id              CHAR(36)     NOT NULL,
                collection      VARCHAR(64)  NULL,
                layout          VARCHAR(64)  NULL,
                layout_options  JSON         NULL,
                layout_query    JSON         NULL,
                filter          JSON         NULL,
                search          VARCHAR(255) NULL,
                bookmark        VARCHAR(255) NULL,
                user_id         VARCHAR(36)  NULL,
                created_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_presets_collection (collection),
                INDEX idx_presets_user_id (user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE presets');
    }
}
