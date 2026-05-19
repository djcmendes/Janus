<?php

/**
 * @file DeploymentsControllerCreateTest.php
 *
 * Tests for DeploymentsController::create().
 *
 * @package App\Deployments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller\Tests;

use App\Deployments\Presentation\Controller\DeploymentsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the create action persists a provider and returns HTTP 201, or a validation error.
 */
#[CoversClass(className: DeploymentsController::class)]
final class DeploymentsControllerCreateTest extends DeploymentsControllerTest
{
    /**
     * Builds a POST Request with a valid JSON body.
     *
     * @param array<string, mixed> $body
     *
     * @return Request
     */
    private function makeRequest(array $body = []): Request
    {
        $defaults = [
            'name'     => 'Netlify Production',
            'type'     => 'netlify',
            'url'      => 'https://api.netlify.com/build_hooks/abc123',
            'isActive' => true,
        ];

        return Request::create(
            uri:     '/deployments',
            method:  'POST',
            content: json_encode(array_merge($defaults, $body)),
        );
    }

    /**
     * Test that create() returns HTTP 201 with a data key on valid input.
     */
    public function testCreateReturns201OnValidInput(): void
    {
        $response = $this->class->create($this->makeRequest(), $this->createDeploymentHandler);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that create() returns HTTP 422 when the name is missing.
     */
    public function testCreateReturns422WhenNameMissing(): void
    {
        $request = Request::create(
            uri:     '/deployments',
            method:  'POST',
            content: json_encode(['type' => 'netlify', 'url' => 'https://example.com']),
        );

        $response = $this->class->create($request, $this->createDeploymentHandler);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that create() returns HTTP 422 when the type is invalid.
     */
    public function testCreateReturns422WhenTypeInvalid(): void
    {
        $request = Request::create(
            uri:     '/deployments',
            method:  'POST',
            content: json_encode(['name' => 'Test', 'type' => 'invalid', 'url' => 'https://example.com']),
        );

        $response = $this->class->create($request, $this->createDeploymentHandler);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that create() throws UnauthorizedException when the guard rejects the request.
     */
    public function testCreateThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->create($this->makeRequest(), $this->createDeploymentHandler);
    }
}
