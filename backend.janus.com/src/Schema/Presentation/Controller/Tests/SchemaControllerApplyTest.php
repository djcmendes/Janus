<?php

/**
 * @file SchemaControllerApplyTest.php
 *
 * Tests for SchemaController::apply().
 *
 * @package App\Schema\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Schema\Presentation\Controller\SchemaController;
use App\Schema\Presentation\DTO\ApplySchemaRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies apply() applies the schema, rejects invalid input, and enforces authorization.
 */
#[CoversClass(className: SchemaController::class)]
#[CoversMethod(SchemaController::class, 'apply')]
final class SchemaControllerApplyTest extends SchemaControllerTest
{
    public function testApplyReturns200OnValidInput(): void
    {
        $dto           = new ApplySchemaRequest();
        $dto->snapshot = ['version' => 1, 'collections' => [], 'relations' => []];

        $this->serializer->method('deserialize')->willReturn($dto);
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());

        $request    = $this->jsonRequest(['snapshot' => ['version' => 1, 'collections' => [], 'relations' => []]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->apply($request, $this->applyHandler);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testApplyResponseHasDataKey(): void
    {
        $dto           = new ApplySchemaRequest();
        $dto->snapshot = ['version' => 1, 'collections' => [], 'relations' => []];

        $this->serializer->method('deserialize')->willReturn($dto);
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());
        $this->applyHandlerReturn = ['applied' => ['create_collection:articles'], 'skipped' => []];

        $request    = $this->jsonRequest(['snapshot' => []]);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->apply($request, $this->applyHandler)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testApplyReturns422WhenValidationFails(): void
    {
        $dto = new ApplySchemaRequest();

        $fakeViolations = new class implements \Countable {
            public function count(): int { return 1; }
            public function get(int $i): object {
                return new class { public function getMessage(): string { return 'snapshot must not be null.'; } };
            }
        };

        $this->serializer->method('deserialize')->willReturn($dto);
        $this->validator->method('validate')->willReturn($fakeViolations);

        $request    = $this->jsonRequest([]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->apply($request, $this->applyHandler);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testApplyReturns422WhenHandlerThrowsInvalidArgumentException(): void
    {
        $dto           = new ApplySchemaRequest();
        $dto->snapshot = [];

        $this->serializer->method('deserialize')->willReturn($dto);
        $this->validator->method('validate')->willReturn($this->makeEmptyViolations());
        $this->applyHandlerThrow = new \InvalidArgumentException('Invalid snapshot format.');

        $request    = $this->jsonRequest(['snapshot' => []]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->apply($request, $this->applyHandler);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('SCHEMA_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testApplyThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['snapshot' => []]);
        $this->class->apply($request, $this->applyHandler);
    }

    public function testApplyThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->apply($this->jsonRequest(['snapshot' => []]), $this->applyHandler);
    }
}
