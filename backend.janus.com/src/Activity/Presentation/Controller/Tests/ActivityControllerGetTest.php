<?php

/**
 * @file ActivityControllerGetTest.php
 *
 * Tests for ActivityController::get().
 *
 * @package App\Activity\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Presentation\Controller\Tests;

use App\Activity\Presentation\Controller\ActivityController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests for ActivityController::get().
 *
 * Covers: happy path, DTO field mapping, UUID forwarding to the repository,
 * not-found handling, guard failures, and access-control enforcement.
 */
#[CoversClass(ActivityController::class)]
#[CoversMethod(ActivityController::class, 'get')]
final class ActivityControllerGetTest extends ActivityControllerTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // ── Happy path ────────────────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 200 with the activity record wrapped in a data envelope.
     */
    public function testGetReturnsActivityForValidId(): void
    {
        $this->getByIdRepository->method('findById')
                                ->willReturn($this->makeActivity());

        $response = $this->class->get(self::LOOKUP_UUID);
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that get() maps all Activity fields into the data envelope.
     */
    public function testGetResponseBodyContainsAllActivityFields(): void
    {
        $activity = $this->makeActivity('update', 'articles', '42');
        $this->getByIdRepository->method('findById')
                                ->willReturn($activity);

        $body = json_decode($this->class->get(self::LOOKUP_UUID)->getContent(), true);

        $this->assertSame((string) $activity->getId(), $body['data']['id']);
        $this->assertSame('update', $body['data']['action']);
        $this->assertSame('articles', $body['data']['collection']);
        $this->assertSame('42', $body['data']['item']);
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $body['data']['user']);
        $this->assertSame('127.0.0.1', $body['data']['ip']);
        $this->assertSame('PHPUnit', $body['data']['user_agent']);
        $this->assertArrayHasKey('timestamp', $body['data']);
    }

    /**
     * Test that get() passes the provided UUID to the repository lookup.
     */
    public function testGetPassesUuidToRepository(): void
    {
        $this->getByIdRepository->expects($this->once())
                                ->method('findById')
                                ->with(self::LOOKUP_UUID)
                                ->willReturn($this->makeActivity());

        $this->class->get(self::LOOKUP_UUID);
    }

    // ── Not found ─────────────────────────────────────────────────────────────

    /**
     * Test that get() returns HTTP 404 when the repository returns null for the given UUID.
     */
    public function testGetReturnsNotFoundWhenActivityDoesNotExist(): void
    {
        $this->getByIdRepository->method('findById')
                                ->willReturn(null);

        $response = $this->class->get(self::LOOKUP_UUID);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test that get() returns a NOT_FOUND error code in the errors envelope on 404.
     */
    public function testGetNotFoundResponseContainsErrorCode(): void
    {
        $this->getByIdRepository->method('findById')
                                ->willReturn(null);

        $body = json_decode($this->class->get(self::LOOKUP_UUID)->getContent(), true);

        $this->assertArrayHasKey('errors', $body);
        $this->assertNotEmpty($body['errors']);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that get() includes the UUID in the error message on 404.
     */
    public function testGetNotFoundErrorMessageContainsUuid(): void
    {
        $this->getByIdRepository->method('findById')
                                ->willReturn(null);

        $body = json_decode($this->class->get(self::LOOKUP_UUID)->getContent(), true);

        $this->assertStringContainsString(self::LOOKUP_UUID, $body['errors'][0]['message']);
    }

    // ── Guard failures ────────────────────────────────────────────────────────

    /**
     * Test that get() propagates UnauthorizedException when no authentication token exists.
     */
    public function testGetThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('This endpoint requires authentication.');

        $this->buildControllerWithUnauthenticatedGuard()->get(self::LOOKUP_UUID);
    }

    /**
     * Test that get() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testGetThrowsWhenClientIsNotAuthorized(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()->get(self::LOOKUP_UUID);
    }

    // ── Access control ────────────────────────────────────────────────────────

    /**
     * Test that get() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testGetThrowsWhenUserLacksAdminRole(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->buildControllerWithAccessDenied()->get(self::LOOKUP_UUID);
    }
}
