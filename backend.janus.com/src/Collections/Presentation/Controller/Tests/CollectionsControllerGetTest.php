<?php

/**
 * @file CollectionsControllerGetTest.php
 *
 * Tests for CollectionsController::get().
 *
 * @package App\Collections\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\Controller\Tests;

use App\Collections\Presentation\Controller\CollectionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Response;

/**
 * Covers: 200 data envelope, field mapping, name forwarding, 404 handling,
 * NOT_FOUND error code, and guard/auth failures.
 */
#[CoversClass(className: CollectionsController::class)]
#[CoversMethod(CollectionsController::class, 'get')]
final class CollectionsControllerGetTest extends CollectionsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 200 with the collection wrapped in a data envelope.
     */
    public function testGetReturns200WithDataEnvelope(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());

        $response = $this->class->get('articles');
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that get() maps all CollectionMeta fields into the data envelope.
     */
    public function testGetResponseContainsCollectionFields(): void
    {
        $this->repository->method('findByName')->willReturn($this->makeCollectionMeta());

        $body = json_decode($this->class->get('articles')->getContent(), true);

        $this->assertArrayHasKey('id', $body['data']);
        $this->assertSame('articles', $body['data']['collection']);
        $this->assertSame('Articles', $body['data']['label']);
    }

    /**
     * Test that get() passes the route name to the repository lookup.
     */
    public function testGetPassesNameToRepository(): void
    {
        $this->repository->expects($this->once())
                         ->method('findByName')
                         ->with('articles')
                         ->willReturn($this->makeCollectionMeta());

        $this->class->get('articles');
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that get() returns HTTP 404 when no collection exists with the given name.
     */
    public function testGetReturns404WhenCollectionNotFound(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $response = $this->class->get('nonexistent');

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that get() returns a NOT_FOUND error code in the errors envelope on 404.
     */
    public function testGetNotFoundResponseContainsNotFoundCode(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $body = json_decode($this->class->get('nonexistent')->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that get() includes the collection name in the 404 error message.
     */
    public function testGetNotFoundErrorMessageContainsName(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $body = json_decode($this->class->get('nonexistent')->getContent(), true);

        $this->assertStringContainsString('nonexistent', $body['errors'][0]['message']);
    }

    // Guard / auth failures ────────────────────────────────────────

    /**
     * Test that get() propagates UnauthorizedException when no authentication token exists.
     */
    public function testGetThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthenticatedGuard()->get('articles');
    }

    /**
     * Test that get() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testGetThrowsWhenClientNotAllowed(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()->get('articles');
    }
}
