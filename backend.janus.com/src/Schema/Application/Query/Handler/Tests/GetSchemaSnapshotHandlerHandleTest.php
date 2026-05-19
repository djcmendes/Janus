<?php

/**
 * @file GetSchemaSnapshotHandlerHandleTest.php
 *
 * Tests for GetSchemaSnapshotHandler::handle().
 *
 * @package App\Schema\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler\Tests;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies handle() delegates to SchemaSnapshotService and returns its result.
 */
#[CoversClass(className: GetSchemaSnapshotHandler::class)]
#[CoversMethod(GetSchemaSnapshotHandler::class, 'handle')]
final class GetSchemaSnapshotHandlerHandleTest extends GetSchemaSnapshotHandlerTest
{
    public function testHandleReturnsSnapshotArray(): void
    {
        $result = $this->class->handle(new GetSchemaSnapshotQuery());

        $this->assertIsArray($result);
    }

    public function testHandleReturnsSnapshotFromService(): void
    {
        $snapshot = ['version' => 1, 'collections' => [['collection' => 'articles']], 'relations' => []];
        $this->snapshotReturn = $snapshot;

        $result = $this->class->handle(new GetSchemaSnapshotQuery());

        $this->assertSame($snapshot, $result);
    }

    public function testHandleCallsSnapshotServiceOnce(): void
    {
        $this->snapshotService->expects($this->once())->method('snapshot');

        $this->class->handle(new GetSchemaSnapshotQuery());
    }
}
