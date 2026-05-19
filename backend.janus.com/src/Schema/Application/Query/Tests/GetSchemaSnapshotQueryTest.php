<?php

/**
 * @file GetSchemaSnapshotQueryTest.php
 *
 * Abstract base for GetSchemaSnapshotQuery test suites.
 *
 * @package App\Schema\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Query\Tests;

use App\Schema\Application\Query\GetSchemaSnapshotQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup for GetSchemaSnapshotQuery tests.
 */
#[CoversClass(className: GetSchemaSnapshotQuery::class)]
abstract class GetSchemaSnapshotQueryTest extends TestCase
{
    protected GetSchemaSnapshotQuery $class;

    public function setUp(): void
    {
        $this->class = new GetSchemaSnapshotQuery();
    }

    public function tearDown(): void
    {
        unset($this->class);
    }
}
