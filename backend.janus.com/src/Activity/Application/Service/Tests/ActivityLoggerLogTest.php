<?php

/**
 * @file ActivityLoggerLogTest.php
 *
 * Tests for ActivityLogger::log().
 *
 * @package App\Activity\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Service\Tests;

use App\Activity\Application\Service\ActivityLogger;
use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests for ActivityLogger::log().
 *
 * Covers: repository record() called with an Activity, userId stored on
 * the entity, IP and User-Agent captured from the current request when
 * available, and IP / User-Agent left as null when no request is active.
 */
#[CoversClass(className:  ActivityLogger::class)]
#[CoversMethod(className: ActivityLogger::class, methodName: 'log')]
final class ActivityLoggerLogTest extends ActivityLoggerTest
{
    /**
     * Test that log() calls repository->record() exactly once with an Activity instance.
     */
    public function testLogCallsRepositoryRecordOnce(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $this->repository->expects(invocationRule: $this->once())
                         ->method(constraint: 'record')
                         ->with(arguments: $this->isInstanceOf(className: Activity::class));

        $this->class->log(action: 'create');
    }

    /**
     * Test that log() completes without throwing.
     */
    public function testLogReturnsVoid(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $this->repository->method(constraint: 'record');

        $this->class->log(action: 'create');

        $this->addToAssertionCount(count: 1);
    }

    /**
     * Test that log() sets the given userId on the Activity entity before persisting.
     */
    public function testLogSetsUserIdOnActivity(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(callback:
                             $this->callback(static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                            })
                         );

        $this->class->log(action: 'create', userId: 'user-uuid');

        $this->assertInstanceOf(expected: Activity::class, actual: $captured);
        $this->assertSame(expected: 'user-uuid', actual: $captured->userId);
    }

    /**
     * Test that log() sets the action, collection, and item on the Activity entity.
     */
    public function testLogSetsActionCollectionAndItemOnActivity(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(
                             arguments: $this->callback(callback: static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                             })
                         );

        $this->class->log(action: 'delete', collection: 'posts', item: '5');

        $this->assertInstanceOf(expected: Activity::class, actual: $captured);
        $this->assertSame(expected: 'delete', actual: $captured->action);
        $this->assertSame(expected: 'posts',  actual: $captured->collection);
        $this->assertSame(expected: '5',      actual: $captured->item);
    }

    /**
     * Test that log() sets the IP address from the current request when one is available.
     */
    public function testLogSetsIpFromCurrentRequestWhenAvailable(): void
    {
        $request = Request::create(
            uri:    '/',
            server: [ 'REMOTE_ADDR' => '192.168.1.1' ]
        );

        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn($request);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(arguments:
                             $this->callback(static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                             })
                         );

        $this->class->log('create');

        $this->assertInstanceOf(expected: Activity::class, actual: $captured);
        $this->assertSame(expected: '192.168.1.1', actual: $captured->ip);
    }

    /**
     * Test that log() sets the User-Agent from the current request when one is available.
     */
    public function testLogSetsUserAgentFromCurrentRequestWhenAvailable(): void
    {
        $request = Request::create(uri: '/');
        $request->headers->set(key: 'User-Agent', values: 'TestBrowser/1.0');
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: $request);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(arguments:
                             $this->callback(static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                            })
                         );

        $this->class->log(action: 'create');

        $this->assertInstanceOf(expected: Activity::class, actual: $captured);
        $this->assertSame(expected: 'TestBrowser/1.0', actual: $captured->userAgent);
    }

    /**
     * Test that log() leaves the IP as null on the Activity when no current request exists.
     */
    public function testLogDoesNotSetIpWhenNoCurrentRequest(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(arguments:
                             $this->callback(static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                             })
                         );

        $this->class->log(action: 'create');

        $this->assertInstanceOf(expected: Activity::class, actual:  $captured);
        $this->assertNull(actual: $captured->ip);
    }

    /**
     * Test that log() leaves the User-Agent as null on the Activity when no current request exists.
     */
    public function testLogDoesNotSetUserAgentWhenNoCurrentRequest(): void
    {
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: null);

        $captured = null;
        $this->repository->method(constraint: 'record')
                         ->with(
                             arguments: $this->callback(static function (Activity $a) use (&$captured): bool {
                                 $captured = $a;
                                 return true;
                            })
                         );

        $this->class->log(action: 'create');

        $this->assertInstanceOf(expected: Activity::class, actual: $captured);
        $this->assertNull(actual: $captured->userAgent);
    }
}
