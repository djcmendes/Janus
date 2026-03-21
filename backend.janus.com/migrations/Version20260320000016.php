<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create extensions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE extensions (
                id          CHAR(36)     NOT NULL,
                name        VARCHAR(255) NOT NULL,
                type        VARCHAR(16)  NOT NULL,
                version     VARCHAR(64)  NOT NULL,
                enabled     TINYINT(1)   NOT NULL DEFAULT 0,
                description TEXT         NULL,
                meta        JSON         NULL,
                created_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                UNIQUE INDEX uniq_extensions_name_type (name, type),
                INDEX idx_extensions_type (type),
                INDEX idx_extensions_enabled (enabled)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE extensions');
    }
}
