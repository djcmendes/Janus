<?php

/**
 * @file ActivityDtoBaseTest.php
 *
 * Constructor and interface compliance tests for ActivityDto.
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionException;

/**
 * This class contains tests for the ActivityDto class.
 */
#[CoversClass(ActivityDto::class)]
final class ActivityDtoBaseTest extends ActivityDtoTest
{
    /**
     * Test that the SUT is an instance of ActivityDto.
     */
    public function testIsInstanceOfActivityDto(): void
    {
        $this->assertInstanceOf(expected: ActivityDto::class, actual: $this->class);
    }

    /**
     * Test that the id property is declared readonly.
     * @throws ReflectionException
     */
    public function testIdPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'id')->isReadOnly());
    }

    /**
     * Test that the action property is declared readonly.
     * @throws ReflectionException
     */
    public function testActionPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'action')->isReadOnly());
    }

    /**
     * Test that the collection property is declared readonly.
     * @throws ReflectionException
     */
    public function testCollectionPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'collection')->isReadOnly());
    }

    /**
     * Test that the item property is declared readonly.
     * @throws ReflectionException
     */
    public function testItemPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'item')->isReadOnly());
    }

    /**
     * Test that the userId property is declared readonly.
     * @throws ReflectionException
     */
    public function testUserIdPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'userId')->isReadOnly());
    }

    /**
     * Test that the ip property is declared readonly.
     * @throws ReflectionException
     */
    public function testIpPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'ip')->isReadOnly());
    }

    /**
     * Test that the userAgent property is declared readonly.
     * @throws ReflectionException
     */
    public function testUserAgentPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'userAgent')->isReadOnly());
    }

    /**
     * Test that the timestamp property is declared readonly.
     * @throws ReflectionException
     */
    public function testTimestampPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'timestamp')->isReadOnly());
    }
}
