<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create shares table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE shares (
                id          CHAR(36)     NOT NULL,
                token       VARCHAR(64)  NOT NULL,
                collection  VARCHAR(64)  NOT NULL,
                item        VARCHAR(255) NOT NULL,
                user_id     VARCHAR(36)  NOT NULL,
                name        VARCHAR(255) NULL,
                password    VARCHAR(255) NULL,
                expires_at  DATETIME     NULL COMMENT '(DC2Type:datetime_immutable)',
                max_uses    INT          NULL,
                times_used  INT          NOT NULL DEFAULT 0,
                created_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                UNIQUE INDEX uniq_shares_token (token),
                INDEX idx_shares_user_id (user_id),
                INDEX idx_shares_collection (collection)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE shares');
    }
}
