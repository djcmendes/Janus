<?php

/**
 * @file GetActivityQueryBaseTest.php
 *
 * Constructor and property compliance tests for GetActivityQuery.
 *
 * @package App\Activity\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Tests;

use App\Activity\Application\Query\GetActivityQuery;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetActivityQuery stores all constructor arguments as readonly properties.
 */
#[CoversClass(className:  GetActivityQuery::class)]
final class GetActivityQueryBaseTest extends GetActivityQueryTest
{
    /**
     * Test that the SUT is an instance of GetActivityQuery.
     */
    public function testIsInstanceOfGetActivityQuery(): void
    {
        $this->assertInstanceOf(expected: GetActivityQuery::class, actual: $this->class);
    }

    /**
     * Test that the constructor stores the limit parameter.
     */
    public function testConstructorStoresLimit(): void
    {
        $this->assertSame(expected: 25, actual: $this->class->limit);
    }

    /**
     * Test that the constructor stores the offset parameter.
     */
    public function testConstructorStoresOffset(): void
    {
        $this->assertSame(expected: 0, actual: $this->class->offset);
    }

    /**
     * Test that collection, action, and userId default to null when omitted.
     */
    public function testFiltersDefaultToNull(): void
    {
        $this->assertNull(actual: $this->class->collection);
        $this->assertNull(actual: $this->class->action);
        $this->assertNull(actual: $this->class->userId);
    }

    /**
     * Test that all optional filter parameters are stored when provided.
     */
    public function testConstructorStoresAllFiltersWhenProvided(): void
    {
        $query = new GetActivityQuery(limit: 10, offset: 5, collection: 'posts', action: 'create', userId: 'user-uuid');

        $this->assertSame(expected: 10,          actual: $query->limit);
        $this->assertSame(expected: 5,           actual: $query->offset);
        $this->assertSame(expected: 'posts',     actual: $query->collection);
        $this->assertSame(expected: 'create',    actual: $query->action);
        $this->assertSame(expected: 'user-uuid', actual: $query->userId);
    }

    /**
     * Test that all properties are declared readonly.
     *
     * @throws \ReflectionException
     */
    public function testAllPropertiesAreReadonly(): void
    {
        foreach (['limit', 'offset', 'collection', 'action', 'userId'] as $property) {
            $this->assertTrue(
                condition: $this->reflection->getProperty(name: $property)->isReadOnly(),
                message:   "Property \${$property} must be readonly.",
            );
        }
    }
}
