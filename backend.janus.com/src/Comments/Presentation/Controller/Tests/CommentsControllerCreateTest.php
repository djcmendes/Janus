<?php

/**
 * @file CommentsControllerCreateTest.php
 *
 * Tests for CommentsController::create().
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
 * Tests for CommentsController::create() — verifying 201/422 responses.
 */
#[CoversClass(className: CommentsController::class)]
#[CoversMethod(CommentsController::class, 'create')]
final class CommentsControllerCreateTest extends CommentsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that create() returns HTTP 201 with valid input.
     */
    public function testCreateReturnsHttp201WithValidInput(): void
    {
        $this->writeRepository->method('save');

        $request = new Request(
            content: json_encode(['comment' => 'Hello', 'collection' => 'posts', 'item' => '42'])
        );

        $response = $this->class->create($request, $this->createCommentHandler);

        $this->assertSame(201, $response->getStatusCode());
    }

    /**
     * Test that create() response body contains a 'data' key.
     */
    public function testCreateResponseContainsDataKey(): void
    {
        $this->writeRepository->method('save');

        $request = new Request(
            content: json_encode(['comment' => 'Hello', 'collection' => 'posts', 'item' => '42'])
        );

        $response = $this->class->create($request, $this->createCommentHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that create() returns HTTP 422 when 'comment' field is missing.
     */
    public function testCreateReturnsHttp422WhenCommentMissing(): void
    {
        $request  = new Request(content: json_encode(['collection' => 'posts', 'item' => '42']));
        $response = $this->class->create($request, $this->createCommentHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Test that create() returns HTTP 422 when 'collection' field is missing.
     */
    public function testCreateReturnsHttp422WhenCollectionMissing(): void
    {
        $request  = new Request(content: json_encode(['comment' => 'Hi', 'item' => '42']));
        $response = $this->class->create($request, $this->createCommentHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Test that create() returns HTTP 422 when 'item' field is missing.
     */
    public function testCreateReturnsHttp422WhenItemMissing(): void
    {
        $request  = new Request(content: json_encode(['comment' => 'Hi', 'collection' => 'posts']));
        $response = $this->class->create($request, $this->createCommentHandler);

        $this->assertSame(422, $response->getStatusCode());
    }

    /**
     * Test that create() 422 response contains a VALIDATION_ERROR code.
     */
    public function testCreateReturnsValidationErrorCode(): void
    {
        $request  = new Request(content: json_encode(['collection' => 'posts', 'item' => '42']));
        $response = $this->class->create($request, $this->createCommentHandler);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }
}
