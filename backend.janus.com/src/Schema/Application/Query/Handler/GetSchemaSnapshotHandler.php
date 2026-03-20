<?php

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use App\Schema\Domain\Service\SchemaSnapshotService;

final class GetSchemaSnapshotHandler
{
    public function __construct(private readonly SchemaSnapshotService $snapshotService) {}

    public function handle(GetSchemaSnapshotQuery $query): array
    {
        return $this->snapshotService->snapshot();
    }
}
