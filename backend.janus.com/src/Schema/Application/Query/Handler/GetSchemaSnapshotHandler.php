<?php

/**
 * @file GetSchemaSnapshotHandler.php
 *
 * Query handler that returns the full current schema snapshot.
 *
 * @package App\Schema\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use App\Schema\Domain\Service\SchemaSnapshotServiceInterface;

/**
 * Handles GetSchemaSnapshotQuery by delegating to SchemaSnapshotServiceInterface.
 */
final class GetSchemaSnapshotHandler implements GetSchemaSnapshotHandlerInterface
{
    /**
     * @param SchemaSnapshotServiceInterface $snapshotService Service that assembles the snapshot from metadata repos.
     */
    public function __construct(private readonly SchemaSnapshotServiceInterface $snapshotService) {}

    /**
     * Returns a complete schema snapshot array.
     *
     * @param  GetSchemaSnapshotQuery $query Marker query (no parameters).
     * @return array<string, mixed>          Snapshot with version, collections, and relations keys.
     */
    public function handle(GetSchemaSnapshotQuery $query): array
    {
        return $this->snapshotService->snapshot();
    }
}
