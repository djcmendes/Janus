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
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $this->repository->expects($this->once())
                         ->method('record')
                         ->with($this->isInstanceOf(Activity::class));

        $this->class->log('create');
    }

    /**
     * Test that log() completes without throwing.
     */
    public function testLogReturnsVoid(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $this->repository->method('record');

        $this->class->log('create');

        $this->addToAssertionCount(1);
    }

    /**
     * Test that log() sets the given userId on the Activity entity before persisting.
     */
    public function testLogSetsUserIdOnActivity(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('create', null, null, 'user-uuid');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertSame('user-uuid', $captured->userId);
    }

    /**
     * Test that log() sets the action, collection, and item on the Activity entity.
     */
    public function testLogSetsActionCollectionAndItemOnActivity(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('delete', 'posts', '5');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertSame('delete', $captured->action);
        $this->assertSame('posts', $captured->collection);
        $this->assertSame('5', $captured->item);
    }

    /**
     * Test that log() sets the IP address from the current request when one is available.
     */
    public function testLogSetsIpFromCurrentRequestWhenAvailable(): void
    {
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '192.168.1.1']);
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn($request);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('create');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertSame('192.168.1.1', $captured->ip);
    }

    /**
     * Test that log() sets the User-Agent from the current request when one is available.
     */
    public function testLogSetsUserAgentFromCurrentRequestWhenAvailable(): void
    {
        $request = Request::create('/');
        $request->headers->set('User-Agent', 'TestBrowser/1.0');
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn($request);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('create');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertSame('TestBrowser/1.0', $captured->userAgent);
    }

    /**
     * Test that log() leaves the IP as null on the Activity when no current request exists.
     */
    public function testLogDoesNotSetIpWhenNoCurrentRequest(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('create');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertNull($captured->ip);
    }

    /**
     * Test that log() leaves the User-Agent as null on the Activity when no current request exists.
     */
    public function testLogDoesNotSetUserAgentWhenNoCurrentRequest(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $captured = null;
        $this->repository->method('record')
                         ->with($this->callback(static function (Activity $a) use (&$captured): bool {
                             $captured = $a;
                             return true;
                         }));

        $this->class->log('create');

        $this->assertInstanceOf(Activity::class, $captured);
        $this->assertNull($captured->userAgent);
    }
}
