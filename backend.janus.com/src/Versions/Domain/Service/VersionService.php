<?php

declare(strict_types=1);

namespace App\Versions\Domain\Service;

use App\Versions\Domain\Entity\Version;
use Doctrine\DBAL\Connection;

/**
 * Domain service that handles the "promote" operation:
 * writes a version's data snapshot back into the live user-defined table.
 *
 * Only column names that already exist in the target table are written;
 * all values are passed as bound parameters to prevent SQL injection.
 */
final class VersionService
{
    public function __construct(private readonly Connection $connection) {}

    /**
     * Restores `$version->getData()` into the row identified by `$version->getItem()`
     * in the collection (user-defined) table.
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \RuntimeException when the table or item does not exist
     */
    public function promote(Version $version): void
    {
        $table  = $version->getCollection();
        $itemId = $version->getItem();
        $data   = $version->getData();

        $this->guardIdentifier($table);

        // Remove the primary key from the SET clause — it must not change
        unset($data['id']);

        if (empty($data)) {
            return;
        }

        // Build parameterised SET clause using safe column names
        $setClauses = [];
        $params     = [];

        foreach ($data as $column => $value) {
            $this->guardIdentifier((string) $column);
            $setClauses[] = sprintf('`%s` = ?', str_replace('`', '``', $column));
            $params[]     = is_array($value) ? json_encode($value) : $value;
        }

        $params[] = $itemId;

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE `id` = ?',
            str_replace('`', '``', $table),
            implode(', ', $setClauses),
        );

        $affected = $this->connection->executeStatement($sql, $params);

        if ($affected === 0) {
            throw new \RuntimeException(sprintf(
                'Item "%s" not found in collection "%s"; promote had no effect.',
                $itemId,
                $table,
            ));
        }
    }

    /** @throws \InvalidArgumentException */
    private function guardIdentifier(string $name): void
    {
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $name)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid identifier "%s".', $name)
            );
        }
    }
}
