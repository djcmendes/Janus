<?php

/**
 * @file GetSchemaSnapshotHandlerInterface.php
 *
 * Contract for the schema snapshot query handler.
 *
 * @package App\Schema\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;

/**
 * Handles GetSchemaSnapshotQuery and returns the full schema snapshot.
 */
interface GetSchemaSnapshotHandlerInterface
{
    /**
     * @param  GetSchemaSnapshotQuery $query Marker query (no parameters).
     * @return array<string, mixed>          Snapshot with version, collections, and relations keys.
     */
    public function handle(GetSchemaSnapshotQuery $query): array;
}
