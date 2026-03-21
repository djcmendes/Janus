<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create settings table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE `settings` (
                `id`                  INT          NOT NULL AUTO_INCREMENT,
                `project_name`        VARCHAR(255) NOT NULL DEFAULT 'Janus',
                `default_language`    VARCHAR(10)  NOT NULL DEFAULT 'en-US',
                `default_appearance`  VARCHAR(50)  NOT NULL DEFAULT 'auto',
                `project_url`         VARCHAR(255) DEFAULT NULL,
                `project_logo`        VARCHAR(255) DEFAULT NULL,
                `project_color`       VARCHAR(255) DEFAULT NULL,
                `updated_at`          DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `settings`');
    }
}
