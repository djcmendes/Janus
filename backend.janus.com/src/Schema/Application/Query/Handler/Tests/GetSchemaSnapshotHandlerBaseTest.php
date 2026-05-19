<?php

/**
 * @file GetSchemaSnapshotHandlerBaseTest.php
 *
 * Basic structural tests for GetSchemaSnapshotHandler.
 *
 * @package App\Schema\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Handler\Tests;

use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetSchemaSnapshotHandler can be instantiated.
 */
#[CoversClass(className: GetSchemaSnapshotHandler::class)]
final class GetSchemaSnapshotHandlerBaseTest extends GetSchemaSnapshotHandlerTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(GetSchemaSnapshotHandler::class, $this->class);
    }
}
