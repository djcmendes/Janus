<?php

/**
 * @file CollectionsControllerDeleteTest.php
 *
 * Tests for CollectionsController::delete().
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Covers: 204 on success, 404 not found, 422 on system table protection,
 * guard/auth failures, and ROLE_ADMIN enforcement.
 */
#[CoversClass(className: CollectionsController::class)]
#[CoversMethod(CollectionsController::class, 'delete')]
final class CollectionsControllerDeleteTest extends CollectionsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that delete() returns HTTP 204 with no body on successful deletion.
     */
    public function testDeleteReturns204OnSuccess(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);

        $response = $this->class->delete('articles');

        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * Test that delete() calls the repository delete once on success.
     */
    public function testDeleteCallsRepositoryDelete(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);
        $this->repository->expects($this->once())->method('delete');

        $this->class->delete('articles');
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that delete() returns HTTP 404 when no collection exists with the given name.
     */
    public function testDeleteReturns404WhenCollectionNotFound(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $response = $this->class->delete('nonexistent');

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that delete() includes NOT_FOUND error code in the 404 response.
     */
    public function testDeleteNotFoundResponseContainsErrorCode(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $body = json_decode((string) $this->class->delete('nonexistent')->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that delete() returns HTTP 422 when the name identifies a system-protected table.
     *
     * SchemaManagerService::dropTable() throws InvalidArgumentException for system tables.
     * The real SchemaManagerService (backed by a mocked Connection) enforces this check.
     */
    public function testDeleteReturns422WhenNameIsSystemTable(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);

        $response = $this->class->delete('users');

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * Test that delete() includes VALIDATION_ERROR error code in the 422 response.
     */
    public function testDeleteSystemTableResponseContainsValidationErrorCode(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findByName')->willReturn($collection);

        $body = json_decode((string) $this->class->delete('users')->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    // Guard / auth failures ────────────────────────────────────────

    /**
     * Test that delete() propagates UnauthorizedException when no authentication token exists.
     */
    public function testDeleteThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthenticatedGuard()->delete('articles');
    }

    /**
     * Test that delete() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testDeleteThrowsAccessDeniedWithoutAdminRole(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->buildControllerWithAccessDenied()->delete('articles');
    }
}
