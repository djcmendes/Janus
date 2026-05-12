<?php

/**
 * @file VersionsControllerCreateTest.php
 *
 * Tests for VersionsController::create().
 *
 * @package App\Versions\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Versions\Presentation\Controller\VersionsController;
use App\Versions\Presentation\DTO\SaveVersionRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies create() persists a new Version, rejects bad input, and enforces authorization.
 */
#[CoversClass(VersionsController::class)]
#[CoversMethod(VersionsController::class, 'create')]
final class VersionsControllerCreateTest extends VersionsControllerTest
{
    public function testCreateReturns201OnValidInput(): void
    {
        $this->serializer->method('deserialize')->willReturn($this->makeSaveRequest());
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());
        $this->writeRepository->method('save');

        $request    = $this->jsonRequest(['collection' => 'articles', 'item' => 'item-uuid-1', 'key' => 'main', 'data' => ['title' => 'Hello']]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->create($request, $this->saveVersionHandler);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testCreateReturns201WithDataKey(): void
    {
        $this->serializer->method('deserialize')->willReturn($this->makeSaveRequest());
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());
        $this->writeRepository->method('save');

        $request    = $this->jsonRequest(['collection' => 'articles', 'item' => 'item-uuid-1', 'key' => 'main', 'data' => ['title' => 'Hello']]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->create($request, $this->saveVersionHandler);
        $body       = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testCreateReturns422WhenValidationFails(): void
    {
        $dto = new SaveVersionRequest();

        $fakeViolations = new class implements \Countable {
            public function count(): int { return 1; }
            public function get(int $i): object {
                return new class { public function getMessage(): string { return 'This value should not be blank.'; } };
            }
        };

        $this->serializer->method('deserialize')->willReturn($dto);
        $this->validator->method('validate')->willReturn($fakeViolations);

        $request    = $this->jsonRequest([]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->create($request, $this->saveVersionHandler);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testCreateReturns409WhenVersionAlreadyExists(): void
    {
        $this->serializer->method('deserialize')->willReturn($this->makeSaveRequest());
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());
        $this->writeRepository->method('findByCollectionItemAndKey')->willReturn($this->makeVersion());

        $request    = $this->jsonRequest(['collection' => 'articles', 'item' => 'item-uuid-1', 'key' => 'main', 'data' => []]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->create($request, $this->saveVersionHandler);

        $this->assertSame(409, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertSame('CONFLICT', $body['errors'][0]['extensions']['code']);
    }

    public function testCreateThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $request    = $this->jsonRequest(['collection' => 'articles', 'item' => 'item-uuid-1', 'key' => 'main', 'data' => []]);
        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->create($request, $this->saveVersionHandler);
    }

    public function testCreateThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['collection' => 'articles', 'item' => 'item-uuid-1', 'key' => 'main', 'data' => []]);
        $this->class->create($request, $this->saveVersionHandler);
    }
}
