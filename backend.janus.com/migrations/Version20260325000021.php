<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260325000021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add interface and options columns to janus_fields; fix system field hidden/readonly flags';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE janus_fields ADD interface VARCHAR(64) DEFAULT NULL");
        $this->addSql("ALTER TABLE janus_fields ADD options JSON DEFAULT NULL");

        // Set sensible interface defaults for existing fields based on type/name
        $this->addSql("UPDATE janus_fields SET interface = CASE
            WHEN field = 'status'                                         THEN 'dropdown'
            WHEN type IN ('dateTime', 'date', 'time')                     THEN 'datetime'
            WHEN type = 'boolean'                                         THEN 'toggle'
            WHEN type = 'json'                                            THEN 'tags'
            WHEN type = 'text' AND field LIKE '%code%'                    THEN 'code'
            WHEN type = 'text' AND field LIKE '%markdown%'                THEN 'markdown'
            WHEN type = 'text' AND (field LIKE '%wysiwyg%' OR field LIKE '%rich%' OR field LIKE '%html%') THEN 'wysiwyg'
            WHEN type = 'text'                                            THEN 'textarea'
            WHEN type IN ('integer', 'bigInteger', 'float', 'decimal')    THEN 'input'
            WHEN type = 'uuid'                                            THEN 'uuid-field'
            WHEN type = 'hash'                                            THEN 'hash'
            WHEN type = 'csv'                                             THEN 'tags'
            ELSE 'input'
        END
        WHERE interface IS NULL");

        // Set default options for status fields (Draft / Published / Archived)
        $this->addSql("UPDATE janus_fields
            SET options = '{\"choices\":[{\"value\":\"draft\",\"text\":\"Draft\"},{\"value\":\"published\",\"text\":\"Published\"},{\"value\":\"archived\",\"text\":\"Archived\"}]}'
            WHERE field = 'status' AND interface = 'dropdown' AND options IS NULL");

        // Mark system tracking fields as hidden + readonly
        $this->addSql("UPDATE janus_fields
            SET hidden = 1, readonly = 1
            WHERE field IN ('date_created', 'date_updated', 'user_created', 'user_updated')");

        // Mark sort field as hidden
        $this->addSql("UPDATE janus_fields SET hidden = 1 WHERE field = 'sort'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE janus_fields DROP COLUMN interface");
        $this->addSql("ALTER TABLE janus_fields DROP COLUMN options");
        $this->addSql("UPDATE janus_fields SET hidden = 0, readonly = 0
            WHERE field IN ('date_created', 'date_updated', 'user_created', 'user_updated', 'sort')");
    }
}
