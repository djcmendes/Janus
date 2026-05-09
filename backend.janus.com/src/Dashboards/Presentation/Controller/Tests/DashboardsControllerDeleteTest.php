<?php

/**
 * @file DashboardsControllerDeleteTest.php
 *
 * Tests for DashboardsController::delete().
 *
 * @package App\Dashboards\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller\Tests;

use App\Dashboards\Presentation\Controller\DashboardsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies the delete action enforces authentication and ROLE_ADMIN and returns 204/404.
 */
#[CoversClass(DashboardsController::class)]
final class DashboardsControllerDeleteTest extends DashboardsControllerTest
{
    /**
     * Test that delete() throws UnauthorizedException when the guard rejects the request.
     */
    public function testDeleteThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->delete('any-id', $this->deleteDashboardHandler);
    }

    /**
     * Test that delete() throws AccessDeniedException when ROLE_ADMIN is not granted.
     */
    public function testDeleteThrowsWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->delete('any-id', $this->deleteDashboardHandler);
    }

    /**
     * Test that delete() returns HTTP 204 on success.
     */
    public function testDeleteReturnsNoContentOnSuccess(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $this->writeRepository->method('findById')->willReturn($this->makeDashboard());

        $response = $controller->delete('bbbbbbbb-0000-7000-8000-000000000001', $this->deleteDashboardHandler);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test that delete() returns HTTP 404 when the dashboard does not exist.
     */
    public function testDeleteReturnsNotFoundWhenMissing(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $this->writeRepository->method('findById')->willReturn(null);

        $response = $controller->delete('non-existent', $this->deleteDashboardHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that delete() cascades panel deletion before removing the dashboard.
     */
    public function testDeleteCascadesPanelDeletion(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $this->writeRepository->method('findById')->willReturn($this->makeDashboard());

        $this->panelRepository->expects($this->once())
                              ->method('deleteByDashboard')
                              ->with('bbbbbbbb-0000-7000-8000-000000000001');

        $controller->delete('bbbbbbbb-0000-7000-8000-000000000001', $this->deleteDashboardHandler);
    }
}
