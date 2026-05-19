<?php

/**
 * @file DashboardsControllerListTest.php
 *
 * Tests for DashboardsController::list().
 *
 * @package App\Dashboards\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller\Tests;

use App\Dashboards\Presentation\Controller\DashboardsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the list action returns paginated results and enforces authentication.
 */
#[CoversClass(className: DashboardsController::class)]
final class DashboardsControllerListTest extends DashboardsControllerTest
{
    /**
     * Test that list() returns HTTP 200 with data and meta keys.
     */
    public function testListReturnsOkWithDataAndMeta(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([]);
        $this->readRepository->method('countAll')->willReturn(0);

        $response = $this->class->list(new Request(), $this->getDashboardsHandler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    /**
     * Test that list() throws UnauthorizedException when the guard rejects the request.
     */
    public function testListThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->list(new Request(), $this->getDashboardsHandler);
    }

    /**
     * Test that list() includes total_count and filter_count in meta.
     */
    public function testListMetaContainsCounts(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([$this->makeDashboard()]);
        $this->readRepository->method('countAll')->willReturn(1);

        $response = $this->class->list(new Request(), $this->getDashboardsHandler);
        $body     = json_decode((string) $response->getContent(), true);

        $this->assertSame(1, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    /**
     * Test that list() scopes results to the authenticated user's UUID when not admin.
     */
    public function testListScopesToCurrentUserWhenNotAdmin(): void
    {
        $this->readRepository->expects($this->once())
                             ->method('countAll')
                             ->with(self::AUTH_USER_UUID)
                             ->willReturn(0);

        $this->readRepository->method('findPaginated')->willReturn([]);

        $this->class->list(new Request(), $this->getDashboardsHandler);
    }
}
