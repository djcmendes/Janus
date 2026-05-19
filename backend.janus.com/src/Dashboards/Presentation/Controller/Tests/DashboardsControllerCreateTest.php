<?php

/**
 * @file DashboardsControllerCreateTest.php
 *
 * Tests for DashboardsController::create().
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
 * Verifies the create action enforces authentication, ROLE_ADMIN, and returns HTTP 201 on success.
 */
#[CoversClass(className: DashboardsController::class)]
final class DashboardsControllerCreateTest extends DashboardsControllerTest
{
    /**
     * Test that create() throws UnauthorizedException when the guard rejects the request.
     */
    public function testCreateThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->create(new Request(), $this->createDashboardHandler);
    }

    /**
     * Test that create() throws AccessDeniedException when ROLE_ADMIN is not granted.
     */
    public function testCreateThrowsWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->create(new Request(), $this->createDashboardHandler);
    }

    /**
     * Test that create() returns HTTP 201 on success (requires admin + valid body).
     */
    public function testCreateReturns201OnSuccess(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $request = new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => 'web'],
            content: json_encode(['name' => 'New Dashboard']),
        );

        $response = $controller->create($request, $this->createDashboardHandler);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * Test that create() returns HTTP 422 when the body is missing the required name field.
     */
    public function testCreateReturns422WhenNameMissing(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $request = new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => 'web'],
            content: json_encode(['icon' => 'chart']),
        );

        $response = $controller->create($request, $this->createDashboardHandler);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that create() wraps the created dashboard DTO under a "data" key.
     */
    public function testCreateWrapsResultUnderDataKey(): void
    {
        $controller = $this->buildControllerWithAdminGuard();

        $request = new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => 'web'],
            content: json_encode(['name' => 'My Dashboard']),
        );

        $response = $controller->create($request, $this->createDashboardHandler);
        $body     = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }
}
