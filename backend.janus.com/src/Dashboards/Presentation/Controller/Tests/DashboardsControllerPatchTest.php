<?php

/**
 * @file DashboardsControllerPatchTest.php
 *
 * Tests for DashboardsController::patch().
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies the patch action enforces authentication and ROLE_ADMIN and returns 200/404.
 */
#[CoversClass(DashboardsController::class)]
final class DashboardsControllerPatchTest extends DashboardsControllerTest
{
    /**
     * Test that patch() throws UnauthorizedException when the guard rejects the request.
     */
    public function testPatchThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->patch('any-id', new Request(), $this->updateDashboardHandler);
    }

    /**
     * Test that patch() throws AccessDeniedException when ROLE_ADMIN is not granted.
     */
    public function testPatchThrowsWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->patch('any-id', new Request(), $this->updateDashboardHandler);
    }

    /**
     * Test that patch() returns HTTP 200 on success (admin + existing dashboard).
     */
    public function testPatchReturns200OnSuccess(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $this->writeRepository->method('findById')->willReturn($this->makeDashboard());

        $request = new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => 'web'],
            content: json_encode(['name' => 'Updated Name']),
        );

        $response = $controller->patch('bbbbbbbb-0000-7000-8000-000000000001', $request, $this->updateDashboardHandler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that patch() returns HTTP 404 when the dashboard does not exist.
     */
    public function testPatchReturns404WhenNotFound(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $this->writeRepository->method('findById')->willReturn(null);

        $response = $controller->patch('non-existent', new Request(), $this->updateDashboardHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
