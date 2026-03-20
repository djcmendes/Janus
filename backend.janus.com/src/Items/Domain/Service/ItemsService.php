<?php

declare(strict_types=1);

namespace App\Items\Domain\Service;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Items\Domain\Exception\ItemNotFoundException;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

/**
 * Dynamic CRUD service for user-defined collections.
 * Uses raw DBAL queries — no Doctrine ORM entities.
 */
final class ItemsService
{
    public function __construct(
        private readonly Connection                         $connection,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
    ) {}

    /**
     * @return array{data: array[], total: int}
     */
    public function findAll(string $collection, int $limit, int $offset): array
    {
        $table = $this->quoteIdentifier($collection);

        $total = (int) $this->connection->fetchOne("SELECT COUNT(*) FROM {$table}");

        $rows = $this->connection->fetchAllAssociative(
            "SELECT * FROM {$table} ORDER BY id ASC LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset],
        );

        return [
            'data'  => array_map($this->normalizeRow(...), $rows),
            'total' => $total,
        ];
    }

    /**
     * @throws ItemNotFoundException
     */
    public function findById(string $collection, string $id): array
    {
        $table  = $this->quoteIdentifier($collection);
        $binary = $this->idToBinary($id);

        $row = $this->connection->fetchAssociative(
            "SELECT * FROM {$table} WHERE id = :id",
            ['id' => $binary],
        );

        if ($row === false) {
            throw new ItemNotFoundException($collection, $id);
        }

        return $this->normalizeRow($row);
    }

    /**
     * @throws \InvalidArgumentException if collection has no fields defined
     */
    public function create(string $collection, array $data): array
    {
        $allowedFields = $this->getAllowedFields($collection);
        $filtered      = $this->filterData($data, $allowedFields);

        $uuid   = Uuid::v7();
        $binary = $uuid->toBinary();

        $columns = ['`id`'];
        $params  = ['id' => $binary];
        $types   = [];

        foreach ($filtered as $column => $value) {
            $columns[] = $this->quoteIdentifier($column);
            $params[$column] = $value;
        }

        $placeholders = implode(', ', array_map(fn ($k) => ":$k", array_keys($params)));
        $columnList   = implode(', ', $columns);
        $table        = $this->quoteIdentifier($collection);

        $this->connection->executeStatement(
            "INSERT INTO {$table} ({$columnList}) VALUES ({$placeholders})",
            $params,
        );

        return $this->findById($collection, (string) $uuid);
    }

    /**
     * @throws ItemNotFoundException
     */
    public function update(string $collection, string $id, array $data): array
    {
        $allowedFields = $this->getAllowedFields($collection);
        $filtered      = $this->filterData($data, $allowedFields);

        if (empty($filtered)) {
            return $this->findById($collection, $id);
        }

        $binary = $this->idToBinary($id);
        $table  = $this->quoteIdentifier($collection);

        $setClauses = [];
        $params     = [];

        foreach ($filtered as $column => $value) {
            $setClauses[] = $this->quoteIdentifier($column) . " = :{$column}";
            $params[$column] = $value;
        }

        $params['id'] = $binary;
        $setStr       = implode(', ', $setClauses);

        $affected = $this->connection->executeStatement(
            "UPDATE {$table} SET {$setStr} WHERE id = :id",
            $params,
        );

        if ($affected === 0) {
            throw new ItemNotFoundException($collection, $id);
        }

        return $this->findById($collection, $id);
    }

    /**
     * @throws ItemNotFoundException
     */
    public function delete(string $collection, string $id): void
    {
        $table  = $this->quoteIdentifier($collection);
        $binary = $this->idToBinary($id);

        $affected = $this->connection->executeStatement(
            "DELETE FROM {$table} WHERE id = :id",
            ['id' => $binary],
        );

        if ($affected === 0) {
            throw new ItemNotFoundException($collection, $id);
        }
    }

    /**
     * Returns allowed non-alias field names for this collection.
     *
     * @return string[]
     */
    private function getAllowedFields(string $collection): array
    {
        $fields = $this->fieldRepository->findByCollection($collection);
        return array_filter(
            array_map(fn ($f) => $f->getType()->isAlias() ? null : $f->getField(), $fields),
        );
    }

    /**
     * Filters $data to only keys present in $allowed. Removes `id` always.
     */
    private function filterData(array $data, array $allowed): array
    {
        unset($data['id']);
        $allowedSet = array_flip($allowed);
        return array_intersect_key($data, $allowedSet);
    }

    /**
     * Converts a binary `id` column value to a UUID string for API output.
     * Handles both 16-byte binary strings and already-hex strings gracefully.
     */
    private function normalizeRow(array $row): array
    {
        if (isset($row['id']) && strlen($row['id']) === 16) {
            $hex      = bin2hex($row['id']);
            $row['id'] = sprintf(
                '%s-%s-%s-%s-%s',
                substr($hex, 0, 8),
                substr($hex, 8, 4),
                substr($hex, 12, 4),
                substr($hex, 16, 4),
                substr($hex, 20),
            );
        }

        return $row;
    }

    /**
     * Converts a UUID string to its 16-byte binary representation.
     *
     * @throws \InvalidArgumentException on invalid UUID
     */
    private function idToBinary(string $id): string
    {
        try {
            return Uuid::fromString($id)->toBinary();
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('Invalid item id "%s".', $id));
        }
    }

    private function quoteIdentifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }
}
