<?php

/**
 * @file GetSchemaSnapshotQueryBaseTest.php
 *
 * Basic structural tests for GetSchemaSnapshotQuery.
 *
 * @package App\Schema\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Tests;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetSchemaSnapshotQuery is instantiable as a zero-argument marker.
 */
#[CoversClass(className: GetSchemaSnapshotQuery::class)]
final class GetSchemaSnapshotQueryBaseTest extends GetSchemaSnapshotQueryTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(GetSchemaSnapshotQuery::class, $this->class);
    }
}
