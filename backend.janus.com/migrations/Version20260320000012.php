<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create notifications table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE notifications (
                id           CHAR(36)     NOT NULL,
                recipient_id VARCHAR(36)  NOT NULL,
                subject      VARCHAR(255) NOT NULL,
                message      LONGTEXT     NOT NULL,
                sender_id    VARCHAR(36)  NULL,
                collection   VARCHAR(64)  NULL,
                item         VARCHAR(255) NULL,
                `read`       TINYINT(1)   NOT NULL DEFAULT 0,
                timestamp    DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                PRIMARY KEY (id),
                INDEX idx_notifications_recipient (recipient_id),
                INDEX idx_notifications_recipient_read (recipient_id, `read`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notifications');
    }
}
