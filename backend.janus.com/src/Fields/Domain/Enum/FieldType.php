<?php

/**
 * @file FieldType.php
 *
 * Backed enum of supported field data types.
 * Includes domain helpers for DDL generation and alias detection.
 *
 * @package App\Fields\Domain\Enum
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Domain\Enum;

/**
 * Supported data types for FieldMeta entries.
 *
 * Each case maps to a backing string value used in the database column
 * and in the REST API. The ALIAS case is virtual — no database column is created.
 */
enum FieldType: string
{
    case STRING   = 'string';
    case TEXT     = 'text';
    case INTEGER  = 'integer';
    case BIG_INT  = 'bigInteger';
    case FLOAT    = 'float';
    case DECIMAL  = 'decimal';
    case BOOLEAN  = 'boolean';
    case UUID     = 'uuid';
    case DATETIME = 'dateTime';
    case DATE     = 'date';
    case TIME     = 'time';
    case JSON     = 'json';
    case CSV      = 'csv';

    /** Virtual/alias — no DB column is created. */
    case ALIAS = 'alias';

    /**
     * Returns true when this type is a virtual alias that produces no database column.
     *
     * @return bool True for ALIAS, false for all other cases.
     */
    public function isAlias(): bool
    {
        return $this === self::ALIAS;
    }

    /**
     * Returns the MySQL column DDL fragment for this type, used by SchemaManagerService.
     *
     * Returns an empty string for ALIAS since no column is created.
     *
     * @return string MySQL column definition fragment (e.g. "VARCHAR(255) DEFAULT NULL").
     */
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
