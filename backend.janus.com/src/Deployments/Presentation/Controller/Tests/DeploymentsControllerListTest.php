<?php

/**
 * @file DeploymentsControllerListTest.php
 *
 * Tests for DeploymentsController::list().
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
 * Verifies the list action returns paginated results and enforces authentication.
 */
#[CoversClass(className: DeploymentsController::class)]
final class DeploymentsControllerListTest extends DeploymentsControllerTest
{
    /**
     * Test that list() returns HTTP 200 with data and meta keys.
     */
    public function testListReturnsOkWithDataAndMeta(): void
    {
        $this->providerRepository->method('findPaginated')->willReturn([]);
        $this->providerRepository->method('countAll')->willReturn(0);

        $response = $this->class->list(new Request(), $this->getDeploymentsHandler);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
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

        $controller->list(new Request(), $this->getDeploymentsHandler);
    }

    /**
     * Test that list() includes total_count and filter_count in meta.
     */
    public function testListMetaContainsCounts(): void
    {
        $this->providerRepository->method('findPaginated')->willReturn([$this->makeProvider()]);
        $this->providerRepository->method('countAll')->willReturn(1);

        $response = $this->class->list(new Request(), $this->getDeploymentsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(1, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    /**
     * Test that list() returns an empty data array when there are no providers.
     */
    public function testListReturnsEmptyData(): void
    {
        $this->providerRepository->method('findPaginated')->willReturn([]);
        $this->providerRepository->method('countAll')->willReturn(0);

        $response = $this->class->list(new Request(), $this->getDeploymentsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame([], $body['data']);
    }
}
