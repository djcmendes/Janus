<?php

/**
 * @file CommentsControllerPatchTest.php
 *
 * Tests for CommentsController::patch().
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
 * Tests for CommentsController::patch() — verifying 200/403/404/422 responses.
 */
#[CoversClass(CommentsController::class)]
#[CoversMethod(CommentsController::class, 'patch')]
final class CommentsControllerPatchTest extends CommentsControllerTest
{
    /** @var string */
    private const string OWNER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that patch() returns HTTP 200 when the owner updates their comment.
     */
    public function testPatchReturnsHttp200ForOwner(): void
    {
        $comment = $this->makeComment('posts', '42', self::OWNER_UUID);
        $this->writeRepository->method('findById')->willReturn($comment);
        $this->writeRepository->method('save');

        $request  = new Request(content: json_encode(['comment' => 'Updated text']));
        $response = $this->class->patch($comment->getId(), $request, $this->updateCommentHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that patch() response body contains a 'data' key.
     */
    public function testPatchResponseContainsDataKey(): void
    {
        $comment = $this->makeComment('posts', '42', self::OWNER_UUID);
        $this->writeRepository->method('findById')->willReturn($comment);
        $this->writeRepository->method('save');

        $request  = new Request(content: json_encode(['comment' => 'Updated text']));
        $response = $this->class->patch($comment->getId(), $request, $this->updateCommentHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that patch() returns HTTP 422 when the comment body is empty.
     */
    public function testPatchReturnsHttp422WhenCommentBodyIsEmpty(): void
    {
        $request  = new Request(content: json_encode(['comment' => '   ']));
        $response = $this->class->patch('any-id', $request, $this->updateCommentHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Test that patch() returns HTTP 404 when the comment does not exist.
     */
    public function testPatchReturnsHttp404WhenCommentNotFound(): void
    {
        $this->writeRepository->method('findById')->willReturn(null);

        $request  = new Request(content: json_encode(['comment' => 'Updated text']));
        $response = $this->class->patch('non-existent-id', $request, $this->updateCommentHandler);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Test that patch() returns HTTP 403 when a non-owner non-admin tries to update.
     */
    public function testPatchReturnsHttp403WhenNonOwnerAttempts(): void
    {
        $comment = $this->makeComment('posts', '42', 'other-owner-uuid');
        $this->writeRepository->method('findById')->willReturn($comment);

        $request  = new Request(content: json_encode(['comment' => 'Hijack']));
        $response = $this->class->patch($comment->getId(), $request, $this->updateCommentHandler);

        $this->assertSame(403, $response->getStatusCode());
    }

    /**
     * Test that patch() 403 response contains a FORBIDDEN error code.
     */
    public function testPatchReturnsForbiddenErrorCode(): void
    {
        $comment = $this->makeComment('posts', '42', 'other-owner-uuid');
        $this->writeRepository->method('findById')->willReturn($comment);

        $request  = new Request(content: json_encode(['comment' => 'Hijack']));
        $response = $this->class->patch($comment->getId(), $request, $this->updateCommentHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame('FORBIDDEN', $body['errors'][0]['extensions']['code']);
    }
}
