<?php

/**
 * @file DeploymentsControllerGetTest.php
 *
 * Tests for DeploymentsController::get().
 *
 * @package App\Deployments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller\Tests;

use App\Deployments\Presentation\Controller\DeploymentsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the get action returns a provider DTO or a 404 error envelope.
 */
#[CoversClass(DeploymentsController::class)]
final class DeploymentsControllerGetTest extends DeploymentsControllerTest
{
    /**
     * Test that get() returns HTTP 200 with a data key when the provider is found.
     */
    public function testGetReturnsOkWithDataWhenFound(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());

        $response = $this->class->get('bbbbbbbb-0000-7000-8000-000000000001', $this->getDeploymentByIdHandler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that get() returns HTTP 404 with an errors envelope when the provider is not found.
     */
    public function testGetReturns404WhenNotFound(): void
    {
        $this->providerRepository->method('findById')->willReturn(null);

        $response = $this->class->get('nonexistent-id', $this->getDeploymentByIdHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that get() throws UnauthorizedException when the guard rejects the request.
     */
    public function testGetThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->get('any-id', $this->getDeploymentByIdHandler);
    }
}
