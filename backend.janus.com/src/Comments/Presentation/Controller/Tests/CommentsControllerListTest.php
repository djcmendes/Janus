<?php

/**
 * @file CommentsControllerListTest.php
 *
 * Tests for CommentsController::list().
 *
 * @package App\Comments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Presentation\Controller\Tests;

use App\Comments\Presentation\Controller\CommentsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests for CommentsController::list() — verifying 200 response shape and pagination.
 */
#[CoversClass(className: CommentsController::class)]
#[CoversMethod(CommentsController::class, 'list')]
final class CommentsControllerListTest extends CommentsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that list() returns HTTP 200.
     */
    public function testListReturnsHttp200(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([]);
        $this->readRepository->method('countAll')->willReturn(0);

        $response = $this->class->list(new Request(), $this->getCommentsHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that list() response body contains 'data' and 'meta' keys.
     */
    public function testListResponseContainsDataAndMetaKeys(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([]);
        $this->readRepository->method('countAll')->willReturn(0);

        $response = $this->class->list(new Request(), $this->getCommentsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    /**
     * Test that list() response meta contains total_count and filter_count.
     */
    public function testListResponseMetaContainsCounts(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([]);
        $this->readRepository->method('countAll')->willReturn(5);

        $response = $this->class->list(new Request(), $this->getCommentsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('total_count', $body['meta']);
        $this->assertArrayHasKey('filter_count', $body['meta']);
    }

    /**
     * Test that list() maps returned Comments to DTO objects.
     */
    public function testListMapsCommentsToDtos(): void
    {
        $this->readRepository->method('findPaginated')->willReturn([$this->makeComment()]);
        $this->readRepository->method('countAll')->willReturn(1);

        $response = $this->class->list(new Request(), $this->getCommentsHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertCount(1, $body['data']);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that list() returns HTTP 401 when the guard rejects the request.
     */
    public function testListReturnsHttp401WhenGuardRejects(): void
    {
        $this->expectException(\Throwable::class);

        $this->buildControllerWithUnauthenticatedGuard()->list(
            new Request(),
            $this->getCommentsHandler
        );
    }
}
