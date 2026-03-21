<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create comments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE comments (
                id          CHAR(36)     NOT NULL,
                collection  VARCHAR(64)  NOT NULL,
                item        VARCHAR(255) NOT NULL,
                comment     LONGTEXT     NOT NULL,
                user_id     VARCHAR(36)  NOT NULL,
                created_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at  DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_comments_collection_item (collection, item),
                INDEX idx_comments_user_id (user_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE comments');
    }
}
