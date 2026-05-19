<?php

/**
 * @file UtilsControllerCacheClearTest.php
 *
 * Tests for UtilsController::cacheClear().
 *
 * @package App\Utils\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Utils\Presentation\Controller\UtilsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies cacheClear() flushes the cache pool and enforces authorization.
 */
#[CoversClass(className: UtilsController::class)]
#[CoversMethod(UtilsController::class, 'cacheClear')]
final class UtilsControllerCacheClearTest extends UtilsControllerTest
{
    public function testCacheClearReturns200(): void
    {
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->cacheClear($this->cache);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testCacheClearResponseHasDataKey(): void
    {
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->cacheClear($this->cache)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testCacheClearResponseDataHasClearedTrue(): void
    {
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->cacheClear($this->cache)->getContent(), true);

        $this->assertTrue($body['data']['cleared']);
    }

    public function testCacheClearCallsCacheClearMethod(): void
    {
        $this->cache->expects($this->once())->method('clear');

        $controller = $this->buildControllerWithAdminGuard();
        $controller->cacheClear($this->cache);
    }

    public function testCacheClearThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->cacheClear($this->cache);
    }

    public function testCacheClearThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->cacheClear($this->cache);
    }
}
