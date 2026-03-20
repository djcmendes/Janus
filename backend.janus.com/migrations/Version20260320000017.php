<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create translations table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE translations (
                id              CHAR(36)     NOT NULL,
                language        VARCHAR(16)  NOT NULL,
                translation_key VARCHAR(255) NOT NULL,
                value           LONGTEXT     NOT NULL,
                created_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at      DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                UNIQUE INDEX uniq_translations_language_key (language, translation_key),
                INDEX idx_translations_language (language)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE translations');
    }
}
