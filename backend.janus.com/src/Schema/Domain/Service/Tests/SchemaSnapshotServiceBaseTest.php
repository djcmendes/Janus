<?php

/**
 * @file SchemaSnapshotServiceBaseTest.php
 *
 * Basic structural tests for SchemaSnapshotService.
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Schema\Domain\Service\SchemaSnapshotService;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies SchemaSnapshotService can be instantiated.
 */
#[CoversClass(className: SchemaSnapshotService::class)]
final class SchemaSnapshotServiceBaseTest extends SchemaSnapshotServiceTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(SchemaSnapshotService::class, $this->class);
    }
}
