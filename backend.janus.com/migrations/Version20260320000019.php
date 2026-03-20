<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create deployment_providers and deployments tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE deployment_providers (
                id         BINARY(16)   NOT NULL,
                name       VARCHAR(255) NOT NULL,
                type       VARCHAR(20)  NOT NULL,
                url        TEXT         NOT NULL,
                options    LONGTEXT     DEFAULT NULL COMMENT '(DC2Type:json)',
                is_active  TINYINT(1)   NOT NULL DEFAULT 1,
                created_at DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE deployments (
                id           BINARY(16)  NOT NULL,
                provider_id  VARCHAR(36) NOT NULL,
                status       VARCHAR(20) NOT NULL,
                log          LONGTEXT    DEFAULT NULL,
                triggered_by VARCHAR(36) DEFAULT NULL,
                started_at   DATETIME    NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                completed_at DATETIME    DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                KEY idx_deployment_provider (provider_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE deployments');
        $this->addSql('DROP TABLE deployment_providers');
    }
}
