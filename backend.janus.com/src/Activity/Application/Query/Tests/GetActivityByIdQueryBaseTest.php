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
use Error;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionException;

/**
 *
 */
#[CoversClass(GetActivityByIdQuery::class)]
final class GetActivityByIdQueryBaseTest extends GetActivityByIdQueryTest
{
    /**
     *
     */
    public function testIsInstanceOfGetActivityByIdQuery(): void
    {
        $this->assertInstanceOf(expected: GetActivityByIdQuery::class, actual: $this->class);
    }

    /**
     *
     */
    public function testConstructorStoresId(): void
    {
        $this->assertSame(expected: self::LOOKUP_UUID, actual: $this->class->id);
    }

    /**
     *
     * @throws ReflectionException
     */
    public function testIdPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'id')->isReadOnly());
    }

    /**
     *
     */
    public function testIdPropertyCannotBeMutatedAfterConstruction(): void
    {
        $this->expectException(exception: Error::class);

        // @phpstan-ignore-next-line
        $this->class->id = 'mutated';
    }
}
