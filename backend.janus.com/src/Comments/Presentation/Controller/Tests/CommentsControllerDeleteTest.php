<?php

/**
 * @file CommentsControllerDeleteTest.php
 *
 * Tests for CommentsController::delete().
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
 * Tests for CommentsController::delete() — verifying 204/403/404 responses.
 */
#[CoversClass(CommentsController::class)]
#[CoversMethod(CommentsController::class, 'delete')]
final class CommentsControllerDeleteTest extends CommentsControllerTest
{
    /** @var string */
    private const string OWNER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that delete() returns HTTP 204 when the owner deletes their comment.
     */
    public function testDeleteReturnsHttp204ForOwner(): void
    {
        $comment = $this->makeComment('posts', '42', self::OWNER_UUID);
        $this->writeRepository->method('findById')->willReturn($comment);
        $this->writeRepository->method('delete');

        $response = $this->class->delete($comment->getId(), new Request(), $this->deleteCommentHandler);

        $this->assertSame(204, $response->getStatusCode());
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that delete() returns HTTP 404 when the comment does not exist.
     */
    public function testDeleteReturnsHttp404WhenCommentNotFound(): void
    {
        $this->writeRepository->method('findById')->willReturn(null);

        $response = $this->class->delete('non-existent-id', new Request(), $this->deleteCommentHandler);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Test that delete() returns HTTP 403 when a non-owner non-admin tries to delete.
     */
    public function testDeleteReturnsHttp403WhenNonOwnerAttempts(): void
    {
        $comment = $this->makeComment('posts', '42', 'other-owner-uuid');
        $this->writeRepository->method('findById')->willReturn($comment);

        $response = $this->class->delete($comment->getId(), new Request(), $this->deleteCommentHandler);

        $this->assertSame(403, $response->getStatusCode());
    }

    /**
     * Test that delete() 403 response contains a FORBIDDEN error code.
     */
    public function testDeleteReturnsForbiddenErrorCode(): void
    {
        $comment = $this->makeComment('posts', '42', 'other-owner-uuid');
        $this->writeRepository->method('findById')->willReturn($comment);

        $response = $this->class->delete($comment->getId(), new Request(), $this->deleteCommentHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame('FORBIDDEN', $body['errors'][0]['extensions']['code']);
    }
}
