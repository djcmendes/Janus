<?php

/**
 * @file CollectionsControllerCreateTest.php
 *
 * Tests for CollectionsController::create().
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
 * Covers: 201 created, 409 name conflict, 422 validation failure,
 * guard auth failures, and ROLE_ADMIN enforcement.
 */
#[CoversClass(className: CollectionsController::class)]
#[CoversMethod(CollectionsController::class, 'create')]
final class CollectionsControllerCreateTest extends CollectionsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that create() returns HTTP 201 with the newly created collection in a data envelope.
     */
    public function testCreateReturns201WithCreatedCollection(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $request  = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $response = $this->class->create($request);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
        $this->assertSame('articles', $body['data']['collection']);
    }

    /**
     * Test that create() calls the repository save once on success.
     */
    public function testCreateCallsRepositorySave(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->expects($this->once())->method('save');

        $request = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $this->class->create($request);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that create() returns HTTP 409 when a collection with the same name already exists.
     */
    public function testCreateReturns409WhenNameAlreadyExists(): void
    {
        $existing = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($existing);

        $request  = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $response = $this->class->create($request);

        $this->assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    /**
     * Test that create() includes COLLECTION_EXISTS error code in the 409 response.
     */
    public function testCreateConflictResponseContainsCollectionExistsCode(): void
    {
        $existing = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($existing);

        $request = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $body    = json_decode($this->class->create($request)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('COLLECTION_EXISTS', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that create() returns HTTP 422 when the request body omits the required name field.
     */
    public function testCreateReturns422WhenNameIsMissing(): void
    {
        $request  = new Request([], [], [], [], [], [], json_encode([]));
        $response = $this->class->create($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that create() returns HTTP 422 when the request body is empty.
     */
    public function testCreateReturns422WhenBodyIsEmpty(): void
    {
        $request  = new Request();
        $response = $this->class->create($request);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that create() includes VALIDATION_ERROR code in the 422 response body.
     */
    public function testCreateValidationErrorResponseContainsErrorCode(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]));
        $body    = json_decode($this->class->create($request)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    // Guard / auth failures ────────────────────────────────────────

    /**
     * Test that create() propagates UnauthorizedException when no authentication token exists.
     */
    public function testCreateThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $this->buildControllerWithUnauthenticatedGuard()->create($request);
    }

    /**
     * Test that create() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testCreateThrowsAccessDeniedWithoutAdminRole(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request([], [], [], [], [], [], json_encode(['name' => 'articles']));
        $this->buildControllerWithAccessDenied()->create($request);
    }
}
