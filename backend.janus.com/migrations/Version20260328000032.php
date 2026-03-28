<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260328000032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add webhook_payload column to magnet_runs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE magnet_runs ADD COLUMN webhook_payload JSON NULL DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE magnet_runs DROP COLUMN webhook_payload');
    }
}
