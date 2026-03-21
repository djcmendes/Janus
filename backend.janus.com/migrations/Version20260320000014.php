<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create dashboards and panels tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE dashboards (
                id          CHAR(36)     NOT NULL,
                name        VARCHAR(255) NOT NULL,
                icon        VARCHAR(64)  NULL,
                note        TEXT         NULL,
                user_id     VARCHAR(36)  NULL,
                created_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_dashboards_user_id (user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE panels (
                id           CHAR(36)    NOT NULL,
                dashboard_id CHAR(36)    NOT NULL,
                type         VARCHAR(64) NOT NULL,
                name         VARCHAR(255) NULL,
                note         TEXT         NULL,
                options      JSON         NULL,
                position_x   INT          NOT NULL DEFAULT 0,
                position_y   INT          NOT NULL DEFAULT 0,
                width        INT          NOT NULL DEFAULT 6,
                height       INT          NOT NULL DEFAULT 4,
                created_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_panels_dashboard_id (dashboard_id),
                CONSTRAINT fk_panels_dashboard FOREIGN KEY (dashboard_id) REFERENCES dashboards (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE panels');
        $this->addSql('DROP TABLE dashboards');
    }
}
