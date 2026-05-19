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
#[CoversClass(className:  ActivityController::class)]
#[CoversMethod(className: ActivityController::class, methodName: 'get')]
final class ActivityControllerGetTest extends ActivityControllerTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that get() returns HTTP 200 with the activity record wrapped in a data envelope.
     */
    public function testGetReturnsActivityForValidId(): void
    {
        $this->getByIdRepository->method(constraint: 'findById')
                                ->willReturn(value: $this->makeActivity());

        $response = $this->class->get(id: self::LOOKUP_UUID);
        $body     = json_decode(json: $response->getContent(), associative: true);

        $this->assertSame(expected: Response::HTTP_OK, actual: $response->getStatusCode());
        $this->assertArrayHasKey(key: 'data', array: $body);
    }

    /**
     * Test that get() maps all Activity fields into the data envelope.
     */
    public function testGetResponseBodyContainsAllActivityFields(): void
    {
        $activity = $this->makeActivity(action: 'update', collection: 'articles',  item: '42');
        $this->getByIdRepository->method(constraint: 'findById')
                                ->willReturn(value: $activity);

        $body = json_decode(
            json: $this->class->get(id: self::LOOKUP_UUID)->getContent(),
            associative: true
        );

        $this->assertSame(expected: (string)$activity->id, actual: $body['data']['id']);
        $this->assertSame(expected: 'update', actual: $body['data']['action']);
        $this->assertSame(expected: 'articles', actual: $body['data']['collection']);
        $this->assertSame(expected: '42', actual: $body['data']['item']);
        $this->assertSame(expected: 'bbbbbbbb-0000-7000-8000-000000000002', actual: $body['data']['user']);
        $this->assertSame(expected: '127.0.0.1', actual: $body['data']['ip']);
        $this->assertSame(expected: 'PHPUnit', actual: $body['data']['user_agent']);
        $this->assertArrayHasKey(key: 'timestamp', array: $body['data']);
    }

    /**
     * Test that get() passes the provided UUID to the repository lookup.
     */
    public function testGetPassesUuidToRepository(): void
    {
        $this->getByIdRepository->expects($this->once())
                                ->method(constraint: 'findById')
                                ->with(self::LOOKUP_UUID)
                                ->willReturn(value: $this->makeActivity());

        $this->class->get(id: self::LOOKUP_UUID);
    }

    /**
     * Test that get() returns HTTP 404 when the repository returns null for the given UUID.
     */
    public function testGetReturnsNotFoundWhenActivityDoesNotExist(): void
    {
        $this->getByIdRepository->method(constraint: 'findById')
                                ->willReturn(value: null);

        $response = $this->class->get(id: self::LOOKUP_UUID);

        $this->assertSame(expected: Response::HTTP_NOT_FOUND, actual: $response->getStatusCode());
    }

    /**
     * Test that get() returns a NOT_FOUND error code in the errors envelope on 404.
     */
    public function testGetNotFoundResponseContainsErrorCode(): void
    {
        $this->getByIdRepository->method(constraint: 'findById')
                                ->willReturn(value: null);

        $body = json_decode(
            json: $this->class->get(id: self::LOOKUP_UUID)->getContent(),
            associative: true
        );

        $this->assertArrayHasKey(key: 'errors', array: $body);
        $this->assertNotEmpty(actual: $body['errors']);
        $this->assertSame(expected: 'NOT_FOUND', actual: $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that get() includes the UUID in the error message on 404.
     */
    public function testGetNotFoundErrorMessageContainsUuid(): void
    {
        $this->getByIdRepository->method(constraint: 'findById')
                                ->willReturn(value: null);

        $body = json_decode(json: $this->class->get(id: self::LOOKUP_UUID)->getContent(), associative: true);

        $this->assertStringContainsString(needle: self::LOOKUP_UUID, haystack: $body['errors'][0]['message']);
    }

    /**
     * Test that get() propagates UnauthorizedException when no authentication token exists.
     */
    public function testGetThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(exception: UnauthorizedException::class);
        $this->expectExceptionMessage(message: 'This endpoint requires authentication.');

        $this->buildControllerWithUnauthenticatedGuard()->get(id: self::LOOKUP_UUID);
    }

    /**
     * Test that get() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testGetThrowsWhenClientIsNotAuthorized(): void
    {
        $this->expectException(exception: UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()->get(id: self::LOOKUP_UUID);
    }

    /**
     * Test that get() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testGetThrowsWhenUserLacksAdminRole(): void
    {
        $this->expectException(exception: AccessDeniedException::class);

        $this->buildControllerWithAccessDenied()->get(id: self::LOOKUP_UUID);
    }
}
