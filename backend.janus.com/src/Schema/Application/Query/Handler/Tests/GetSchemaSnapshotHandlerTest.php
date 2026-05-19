<?php

/**
 * @file GetSchemaSnapshotHandlerTest.php
 *
 * Abstract base for GetSchemaSnapshotHandler test suites.
 *
 * @package App\Schema\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler\Tests;

use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandler;
use App\Schema\Domain\Service\SchemaSnapshotServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup for GetSchemaSnapshotHandler tests.
 */
#[CoversClass(className: GetSchemaSnapshotHandler::class)]
abstract class GetSchemaSnapshotHandlerTest extends TestCase
{
    /** @var MockObject&SchemaSnapshotServiceInterface */
    protected MockObject $snapshotService;

    /** @var GetSchemaSnapshotHandler */
    protected GetSchemaSnapshotHandler $class;

    /** @var array<string, mixed> Mutable return value for snapshotService::snapshot() */
    protected array $snapshotReturn = [
        'version' => 1, 'collections' => [], 'relations' => [],
    ];

    public function setUp(): void
    {
        $this->snapshotReturn  = ['version' => 1, 'collections' => [], 'relations' => []];
        $this->snapshotService = $this->createMock(SchemaSnapshotServiceInterface::class);
        $this->snapshotService->method('snapshot')
            ->willReturnCallback(fn() => $this->snapshotReturn);

        $this->class = new GetSchemaSnapshotHandler(snapshotService: $this->snapshotService);
    }

    public function tearDown(): void
    {
        unset($this->snapshotService, $this->class);
    }
}
