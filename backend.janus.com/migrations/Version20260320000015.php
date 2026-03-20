<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create flows and operations tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE flows (
                id              CHAR(36)     NOT NULL,
                name            VARCHAR(255) NOT NULL,
                status          VARCHAR(10)  NOT NULL DEFAULT 'inactive',
                trigger         VARCHAR(16)  NOT NULL DEFAULT 'manual',
                trigger_options JSON         NULL,
                user_id         VARCHAR(36)  NULL,
                description     TEXT         NULL,
                created_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_flows_status (status),
                INDEX idx_flows_trigger (trigger)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE operations (
                id           CHAR(36)     NOT NULL,
                flow_id      CHAR(36)     NOT NULL,
                name         VARCHAR(255) NOT NULL,
                type         VARCHAR(64)  NOT NULL,
                options      JSON         NULL,
                resolve      VARCHAR(255) NULL,
                next_success CHAR(36)     NULL,
                next_failure CHAR(36)     NULL,
                sort_order   INT          NOT NULL DEFAULT 0,
                created_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_operations_flow_id (flow_id),
                INDEX idx_operations_flow_sort (flow_id, sort_order),
                CONSTRAINT fk_operations_flow FOREIGN KEY (flow_id) REFERENCES flows (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE operations');
        $this->addSql('DROP TABLE flows');
    }
}
