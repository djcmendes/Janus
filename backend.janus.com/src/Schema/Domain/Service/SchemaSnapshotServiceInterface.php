<?php

/**
 * @file SchemaSnapshotServiceInterface.php
 *
 * Contract for assembling a complete schema snapshot from metadata repositories.
 *
 * @package App\Schema\Domain\Service
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service;

/**
 * Assembles and returns the current schema snapshot.
 */
interface SchemaSnapshotServiceInterface
{
    /**
     * @return array{version: int, collections: list<array>, relations: list<array>}
     */
    public function snapshot(): array;
}
