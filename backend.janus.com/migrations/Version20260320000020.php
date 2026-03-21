<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260320000020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add TOTP fields to users table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE users ADD totp_secret VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE users ADD totp_enabled TINYINT(1) NOT NULL DEFAULT 0");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE users DROP COLUMN totp_secret");
        $this->addSql("ALTER TABLE users DROP COLUMN totp_enabled");
    }
}
