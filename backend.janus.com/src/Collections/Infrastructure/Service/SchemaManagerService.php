<?php

declare(strict_types=1);

namespace App\Collections\Infrastructure\Service;

use Doctrine\DBAL\Connection;

/**
 * Executes real DDL statements against the database.
 * System tables are protected and cannot be modified via this service.
 */
final class SchemaManagerService
{
    /** Tables that must never be touched by user-initiated DDL */
    private const SYSTEM_TABLES = [
        'users', 'roles', 'settings', 'activity',
        'permissions', 'policies', 'access',
        'janus_collections', 'janus_fields',
        'doctrine_migration_versions',
    ];

    public function __construct(private readonly Connection $connection) {}

    private const PK_TYPE_DDL = [
        'uuid'        => 'BINARY(16) NOT NULL',
        'integer'     => 'INT NOT NULL AUTO_INCREMENT',
        'bigInteger'  => 'BIGINT NOT NULL AUTO_INCREMENT',
        'string'      => 'VARCHAR(255) NOT NULL',
    ];

    /**
     * Creates a new user-defined table with a configurable primary key.
     *
     * @param string $primaryKeyField Column name for the PK (default: 'id')
     * @param string $primaryKeyType  One of: uuid, integer, bigInteger, string (default: 'uuid')
     * @throws \InvalidArgumentException if the table name is protected or invalid
     * @throws \Doctrine\DBAL\Exception
     */
    public function createTable(string $tableName, string $primaryKeyField = 'id', string $primaryKeyType = 'uuid'): void
    {
        $this->guardTableName($tableName);
        $this->guardIdentifier($primaryKeyField);

        if (!array_key_exists($primaryKeyType, self::PK_TYPE_DDL)) {
            throw new \InvalidArgumentException(
                sprintf('Unsupported primary key type "%s". Allowed: %s.', $primaryKeyType, implode(', ', array_keys(self::PK_TYPE_DDL)))
            );
        }

        $quotedTable = $this->quoteIdentifier($tableName);
        $quotedPk    = $this->quoteIdentifier($primaryKeyField);
        $pkDdl       = self::PK_TYPE_DDL[$primaryKeyType];

        $this->connection->executeStatement(<<<SQL
            CREATE TABLE {$quotedTable} (
                {$quotedPk} {$pkDdl},
                PRIMARY KEY ({$quotedPk})
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
    }

    /**
     * Drops a user-defined table.
     *
     * @throws \InvalidArgumentException if the table name is protected or invalid
     * @throws \Doctrine\DBAL\Exception
     */
    public function dropTable(string $tableName): void
    {
        $this->guardTableName($tableName);

        $quoted = $this->quoteIdentifier($tableName);
        $this->connection->executeStatement("DROP TABLE IF EXISTS {$quoted}");
    }

    /**
     * Adds a column to a user-defined table.
     *
     * @throws \InvalidArgumentException if the table/column name is protected or invalid
     * @throws \Doctrine\DBAL\Exception
     */
    public function addColumn(string $tableName, string $columnName, string $columnDdl): void
    {
        $this->guardTableName($tableName);
        $this->guardIdentifier($columnName);

        $quotedTable  = $this->quoteIdentifier($tableName);
        $quotedColumn = $this->quoteIdentifier($columnName);

        $this->connection->executeStatement(
            "ALTER TABLE {$quotedTable} ADD COLUMN {$quotedColumn} {$columnDdl}"
        );
    }

    /**
     * Drops a column from a user-defined table.
     *
     * @throws \InvalidArgumentException if the table/column name is protected or invalid
     * @throws \Doctrine\DBAL\Exception
     */
    public function dropColumn(string $tableName, string $columnName): void
    {
        $this->guardTableName($tableName);
        $this->guardIdentifier($columnName);

        $quotedTable  = $this->quoteIdentifier($tableName);
        $quotedColumn = $this->quoteIdentifier($columnName);

        $this->connection->executeStatement(
            "ALTER TABLE {$quotedTable} DROP COLUMN {$quotedColumn}"
        );
    }

    /** @throws \InvalidArgumentException */
    private function guardTableName(string $name): void
    {
        $this->guardIdentifier($name);

        if (in_array(strtolower($name), self::SYSTEM_TABLES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Table "%s" is a system table and cannot be modified.', $name)
            );
        }
    }

    /** @throws \InvalidArgumentException */
    private function guardIdentifier(string $name): void
    {
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $name)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid identifier "%s". Must start with a letter and contain only letters, digits, or underscores (max 64 chars).', $name)
            );
        }
    }

    private function quoteIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }
}
