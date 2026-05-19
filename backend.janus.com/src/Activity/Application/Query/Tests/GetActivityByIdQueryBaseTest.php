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
use ReflectionProperty;

/**
 * Verifies that GetActivityByIdQuery stores its id correctly after construction.
 */
#[CoversClass(className:  GetActivityByIdQuery::class)]
final class GetActivityByIdQueryBaseTest extends GetActivityByIdQueryTest
{
    /**
     * Test that the constructor stores the lookup UUID in the id property.
     */
    public function testConstructorStoresId(): void
    {
        $this->assertSame(expected: self::LOOKUP_UUID, actual: $this->class->id);
    }

    /**
     * Test that the id property is declared readonly.
     *
     * @throws ReflectionException
     */
    public function testIdPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'id')->isReadOnly());
    }

    /**
     * Test that the id property cannot be mutated after construction.
     * @throws ReflectionException
     */
    public function testIdPropertyCannotBeMutatedAfterConstruction(): void
    {
        $this->expectException(exception: Error::class);

        $reflectionProperty = new ReflectionProperty(class: $this->class, property: 'id');
        $reflectionProperty->setValue(objectOrValue: $this->class, value: 'mutated-uuid-value');
    }
}
