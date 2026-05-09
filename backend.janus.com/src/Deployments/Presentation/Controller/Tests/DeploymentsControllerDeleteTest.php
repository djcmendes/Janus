<?php

/**
 * @file DeploymentsControllerDeleteTest.php
 *
 * Tests for DeploymentsController::delete().
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
 * Verifies the delete action returns HTTP 204 or a 404 error envelope.
 */
#[CoversClass(DeploymentsController::class)]
final class DeploymentsControllerDeleteTest extends DeploymentsControllerTest
{
    /**
     * Test that delete() returns HTTP 204 when the provider is found and removed.
     */
    public function testDeleteReturns204WhenProviderFound(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());

        $response = $this->class->delete('bbbbbbbb-0000-7000-8000-000000000001', $this->deleteDeploymentHandler);

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test that delete() returns HTTP 404 when the provider is not found.
     */
    public function testDeleteReturns404WhenNotFound(): void
    {
        $this->providerRepository->method('findById')->willReturn(null);

        $response = $this->class->delete('nonexistent-id', $this->deleteDeploymentHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that delete() throws UnauthorizedException when the guard rejects the request.
     */
    public function testDeleteThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->delete('any-id', $this->deleteDeploymentHandler);
    }
}
