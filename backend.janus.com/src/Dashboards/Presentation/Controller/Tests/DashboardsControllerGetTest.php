<?php

/**
 * @file DashboardsControllerGetTest.php
 *
 * Tests for DashboardsController::get().
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

/**
 * Verifies the get action returns a single dashboard or a 404 when not found.
 */
#[CoversClass(DashboardsController::class)]
final class DashboardsControllerGetTest extends DashboardsControllerTest
{
    /**
     * Test that get() returns HTTP 200 when the dashboard exists.
     */
    public function testGetReturnsOkWhenFound(): void
    {
        $this->readRepository->method('findById')->willReturn($this->makeDashboard());

        $response = $this->class->get('bbbbbbbb-0000-7000-8000-000000000001', $this->getDashboardByIdHandler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that get() wraps the DTO under a "data" key.
     */
    public function testGetWrapsResultUnderDataKey(): void
    {
        $this->readRepository->method('findById')->willReturn($this->makeDashboard());

        $response = $this->class->get('bbbbbbbb-0000-7000-8000-000000000001', $this->getDashboardByIdHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that get() returns HTTP 404 when the dashboard is not found.
     */
    public function testGetReturnsNotFoundWhenMissing(): void
    {
        $this->readRepository->method('findById')->willReturn(null);

        $response = $this->class->get('non-existent', $this->getDashboardByIdHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that get() throws UnauthorizedException when unauthenticated.
     */
    public function testGetThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->get('any-id', $this->getDashboardByIdHandler);
    }

    /**
     * Test that the 404 response contains the standard error envelope.
     */
    public function testGetNotFoundResponseContainsErrorEnvelope(): void
    {
        $this->readRepository->method('findById')->willReturn(null);

        $response = $this->class->get('missing', $this->getDashboardByIdHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }
}
