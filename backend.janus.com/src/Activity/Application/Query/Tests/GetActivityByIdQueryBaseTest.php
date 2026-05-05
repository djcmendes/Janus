<?php

/**
 * @file GetActivityByIdQueryBaseTest.php
 *
 * Constructor and property compliance tests for GetActivityByIdQuery.
 *
 * @package App\Activity\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Tests;

use App\Activity\Application\Query\GetActivityByIdQuery;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GetActivityByIdQuery::class)]
final class GetActivityByIdQueryBaseTest extends GetActivityByIdQueryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfGetActivityByIdQuery(): void
    {
        $this->assertInstanceOf(GetActivityByIdQuery::class, $this->class);
    }

    public function testConstructorStoresId(): void
    {
        $this->assertSame(self::LOOKUP_UUID, $this->class->id);
    }

    public function testIdPropertyIsReadonly(): void
    {
        $this->assertTrue($this->reflection->getProperty('id')->isReadOnly());
    }

    // Failure / exception paths ────────────────────────────────────

    public function testIdPropertyCannotBeMutatedAfterConstruction(): void
    {
        $this->expectException(\Error::class);

        // @phpstan-ignore-next-line
        $this->class->id = 'mutated';
    }
}
