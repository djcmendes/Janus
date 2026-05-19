<?php

/**
 * @file CommentsControllerGetTest.php
 *
 * Tests for CommentsController::get().
 *
 * @package App\Comments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Presentation\Controller\Tests;

use App\Comments\Presentation\Controller\CommentsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentsController::get() — verifying 200/404 responses.
 */
#[CoversClass(className: CommentsController::class)]
#[CoversMethod(CommentsController::class, 'get')]
final class CommentsControllerGetTest extends CommentsControllerTest
{
    /** @var string */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 200 with the comment data when the comment exists.
     */
    public function testGetReturnsHttp200WhenCommentExists(): void
    {
        $this->readRepository->method('findById')->willReturn($this->makeComment());

        $response = $this->class->get(self::LOOKUP_UUID, $this->getCommentByIdHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test that get() response body contains a 'data' key with comment fields.
     */
    public function testGetResponseContainsDataKey(): void
    {
        $this->readRepository->method('findById')->willReturn($this->makeComment());

        $response = $this->class->get(self::LOOKUP_UUID, $this->getCommentByIdHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('id', $body['data']);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that get() returns HTTP 404 when the comment does not exist.
     */
    public function testGetReturnsHttp404WhenCommentNotFound(): void
    {
        $this->readRepository->method('findById')->willReturn(null);

        $response = $this->class->get('non-existent-id', $this->getCommentByIdHandler);

        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * Test that get() 404 response contains an errors array with NOT_FOUND code.
     */
    public function testGetReturnsNotFoundErrorCode(): void
    {
        $this->readRepository->method('findById')->willReturn(null);

        $response = $this->class->get('non-existent-id', $this->getCommentByIdHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }
}
