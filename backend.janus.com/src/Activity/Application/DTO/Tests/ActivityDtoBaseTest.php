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
#[CoversClass(className: ActivityDto::class)]
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
     * Test that the id property is set correctly.
     */
    public function testIdPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: '123', actual: $activityDto->id);
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
     * Test that the Action property is set correctly.
     */
    public function testActionPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: 'Hadouken', actual: $activityDto->action);
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
     * Test that the collection property is set correctly.
     */
    public function testCollectionPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: 'things', actual: $activityDto->collection);
    }

    /**
     * Test that the collection property is set correctly as null.
     */
    public function testCollectionPropertyIsSetCorrectlyAsNull(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: null, actual: $activityDto->collection);
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
     * Test that the item property is set correctly.
     */
    public function testItemPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       'glass',
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: 'glass', actual: $activityDto->item);
    }

    /**
     * Test that the item property is set correctly as null.
     */
    public function testItemPropertyIsSetCorrectlyAsNull(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: null, actual: $activityDto->item);
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
     * Test that the user id property is set correctly.
     */
    public function testUserIdPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       'glass',
            userId:     'riu',
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: 'riu', actual: $activityDto->userId);
    }

    /**
     * Test that the user id property is set correctly as null.
     */
    public function testUserIdPropertyIsSetCorrectlyAsNull(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );
        $this->assertSame(expected: null, actual: $activityDto->userId);
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
     * Test that the ip property is set correctly.
     */
    public function testIpPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       'glass',
            userId:     'riu',
            ip:         '123.1.1.1',
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );
        $this->assertSame(expected: '123.1.1.1', actual: $activityDto->ip);
    }

    /**
     * Test that the ip property is set correctly as null.
     */
    public function testIpPropertyIsSetCorrectlyAsNull(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );
        $this->assertSame(expected: null, actual: $activityDto->ip);
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
     * Test that the user agent property is set correctly.
     */
    public function testUserAgentPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       'glass',
            userId:     'riu',
            ip:         '123.1.1.1',
            userAgent:  '007',
            timestamp:  '2026-05-09T13:36:39.000Z'
        );
        $this->assertSame(expected: '007', actual: $activityDto->userAgent);
    }

    /**
     * Test that the user agent property is set correctly as null.
     */
    public function testUserAgentPropertyIsSetCorrectlyAsNull(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: null, actual: $activityDto->userAgent);
    }

    /**
     * Test that the timestamp property is declared readonly.
     * @throws ReflectionException
     */
    public function testTimestampPropertyIsReadonly(): void
    {
        $this->assertTrue(condition: $this->reflection->getProperty(name: 'timestamp')->isReadOnly());
    }

    /**
     * Test that the timestamp property is set correctly.
     */
    public function testTimestampPropertyIsSetCorrectly(): void
    {
        $activityDto = new ActivityDto(
            id:         '123',
            action:     'Hadouken',
            collection: 'things',
            item:       'glass',
            userId:     'riu',
            ip:         '123.1.1.1',
            userAgent:  '007',
            timestamp:  '2026-05-09T13:36:39.000Z'
        );

        $this->assertSame(expected: '2026-05-09T13:36:39.000Z', actual: $activityDto->timestamp);
    }
}
