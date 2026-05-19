<?php

/**
 * @file ActivityLoggerLogTest.php
 *
 * Tests for ActivityLogger::log().
 *
 * @package App\Activity\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Service\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Service\ActivityLogger;
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
     * Test that log() returns void and produces no output.
     */
    public function testLogReturnsVoid(): void
    {
        $this->requestStack->method('getCurrentRequest')
                           ->willReturn(null);

        $this->repository->method('record');

        $result = $this->class->log('create');

        $this->assertNull($result);
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

        $this->assertSame('user-uuid', $captured->getUserId());
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

        $this->assertSame('delete', $captured->getAction());
        $this->assertSame('posts', $captured->getCollection());
        $this->assertSame('5', $captured->getItem());
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

        $this->assertSame('192.168.1.1', $captured->getIp());
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

        $this->assertSame('TestBrowser/1.0', $captured->getUserAgent());
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

        $this->assertNull($captured->getIp());
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

        $this->assertNull($captured->getUserAgent());
    }
}
