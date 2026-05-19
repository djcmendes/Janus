<?php

/**
 * @file VersionsControllerListTest.php
 *
 * Tests for VersionsController::list().
 *
 * @package App\Versions\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Versions\Presentation\Controller\VersionsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies list() returns a paginated envelope and enforces authentication.
 */
#[CoversClass(className: VersionsController::class)]
#[CoversMethod(VersionsController::class, 'list')]
final class VersionsControllerListTest extends VersionsControllerTest
{
    public function testListReturnsOk(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([$this->makeVersion()]);
        $this->readRepository->method('countAll')->willReturn(1);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->list($request, $this->getVersionsHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testListResponseHasDataAndMetaKeys(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([$this->makeVersion()]);
        $this->readRepository->method('countAll')->willReturn(1);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->list($request, $this->getVersionsHandler);
        $body       = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    public function testListResponseMetaContainsCounts(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([$this->makeVersion()]);
        $this->readRepository->method('countAll')->willReturn(1);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->list($request, $this->getVersionsHandler);
        $body       = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('total_count', $body['meta']);
        $this->assertArrayHasKey('filter_count', $body['meta']);
    }

    public function testListReturnsEmptyDataWhenNoVersions(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([]);
        $this->readRepository->method('countAll')->willReturn(0);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->list($request, $this->getVersionsHandler);
        $body       = json_decode($response->getContent(), true);

        $this->assertSame([], $body['data']);
    }

    public function testListThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $this->class->list($request, $this->getVersionsHandler);
    }

    public function testListThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->list($request, $this->getVersionsHandler);
    }
}
