<?php

/**
 * @file GetVersionByIdQueryBaseTest.php
 *
 * Tests for GetVersionByIdQuery construction.
 *
 * @package App\Versions\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Tests;

use App\Versions\Application\Query\GetVersionByIdQuery;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetVersionByIdQuery stores the id as a readonly property.
 */
#[CoversClass(className: GetVersionByIdQuery::class)]
final class GetVersionByIdQueryBaseTest extends GetVersionByIdQueryTest
{
    public function testIdIsStored(): void
    {
        $this->assertSame(self::LOOKUP_UUID, $this->class->id);
    }
}
