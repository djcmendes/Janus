<?php

/**
 * @file CollectionsControllerPatchTest.php
 *
 * Tests for CollectionsController::patch().
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Covers: 200 updated collection, 404 not found, guard/auth failures,
 * and ROLE_ADMIN enforcement.
 */
#[CoversClass(className: CollectionsController::class)]
#[CoversMethod(CollectionsController::class, 'patch')]
final class CollectionsControllerPatchTest extends CollectionsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that patch() returns HTTP 200 with the updated collection in a data envelope.
     */
    public function testPatchReturns200WithUpdatedCollection(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);

        $request  = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated Articles']));
        $response = $this->class->patch('articles', $request);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that patch() with an empty body returns 200 leaving all fields unchanged.
     */
    public function testPatchWithEmptyBodyReturns200(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);

        $request  = new Request([], [], [], [], [], [], json_encode([]));
        $response = $this->class->patch('articles', $request);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Test that patch() calls the repository save once on success.
     */
    public function testPatchCallsRepositorySave(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->expects($this->once())->method('save');

        $request = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated']));
        $this->class->patch('articles', $request);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that patch() returns HTTP 404 when no collection exists with the given name.
     */
    public function testPatchReturns404WhenCollectionNotFound(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $request  = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated']));
        $response = $this->class->patch('nonexistent', $request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that patch() includes NOT_FOUND error code in the 404 response.
     */
    public function testPatchNotFoundResponseContainsErrorCode(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $request = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated']));
        $body    = json_decode($this->class->patch('nonexistent', $request)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    // Guard / auth failures ────────────────────────────────────────

    /**
     * Test that patch() propagates UnauthorizedException when no authentication token exists.
     */
    public function testPatchThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated']));
        $this->buildControllerWithUnauthenticatedGuard()->patch('articles', $request);
    }

    /**
     * Test that patch() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testPatchThrowsAccessDeniedWithoutAdminRole(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request([], [], [], [], [], [], json_encode(['label' => 'Updated']));
        $this->buildControllerWithAccessDenied()->patch('articles', $request);
    }
}
