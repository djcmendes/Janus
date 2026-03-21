<?php

declare(strict_types=1);

namespace App\Fields\Domain\Enum;

enum FieldType: string
{
    case STRING     = 'string';
    case TEXT       = 'text';
    case INTEGER    = 'integer';
    case BIG_INT    = 'bigInteger';
    case FLOAT      = 'float';
    case DECIMAL    = 'decimal';
    case BOOLEAN    = 'boolean';
    case UUID       = 'uuid';
    case DATETIME   = 'dateTime';
    case DATE       = 'date';
    case TIME       = 'time';
    case JSON       = 'json';
    case CSV        = 'csv';

    /** Virtual/alias — no DB column is created */
    case ALIAS      = 'alias';

    public function isAlias(): bool
    {
        return $this === self::ALIAS;
    }

    /** Returns the MySQL column DDL fragment for this type */
    public function toColumnDdl(): string
    {
        return match ($this) {
            self::STRING   => 'VARCHAR(255) DEFAULT NULL',
            self::TEXT     => 'LONGTEXT DEFAULT NULL',
            self::INTEGER  => 'INT DEFAULT NULL',
            self::BIG_INT  => 'BIGINT DEFAULT NULL',
            self::FLOAT    => 'FLOAT DEFAULT NULL',
            self::DECIMAL  => 'DECIMAL(15,4) DEFAULT NULL',
            self::BOOLEAN  => 'TINYINT(1) DEFAULT NULL',
            self::UUID     => 'BINARY(16) DEFAULT NULL',
            self::DATETIME => 'DATETIME DEFAULT NULL',
            self::DATE     => 'DATE DEFAULT NULL',
            self::TIME     => 'TIME DEFAULT NULL',
            self::JSON     => 'JSON DEFAULT NULL',
            self::CSV      => 'TEXT DEFAULT NULL',
            self::ALIAS    => '',
        };
    }
}
