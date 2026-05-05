<?php

/**
 * @file ActivityControllerListTest.php
 *
 * Tests for ActivityController::list().
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests for ActivityController::list().
 *
 * Covers: happy path, DTO mapping, meta counts, query-parameter forwarding
 * to the repository, limit capping, guard failures, and access-control.
 */
#[CoversClass(ActivityController::class)]
#[CoversMethod(ActivityController::class, 'list')]
final class ActivityControllerListTest extends ActivityControllerTest
{
    // ── Data Providers ────────────────────────────────────────────────────────

    /**
     * Provides query-string inputs and the findPaginated() arguments the
     * repository is expected to receive after the real handler processes them.
     *
     * @return array<string, array{
     *     params: array<string, string>,
     *     expectedLimit: int,
     *     expectedOffset: int,
     *     expectedCollection: string|null,
     *     expectedAction: string|null,
     *     expectedUserId: string|null,
     * }>
     */
    public static function queryParameterProvider(): array
    {
        return [
            'defaults when no params given' => [
                'params'             => [],
                'expectedLimit'      => 25,
                'expectedOffset'     => 0,
                'expectedCollection' => null,
                'expectedAction'     => null,
                'expectedUserId'     => null,
            ],
            'all filters provided' => [
                'params'             => [
                    'limit'      => '10',
                    'offset'     => '5',
                    'collection' => 'posts',
                    'action'     => 'create',
                    'user'       => 'user-uuid',
                ],
                'expectedLimit'      => 10,
                'expectedOffset'     => 5,
                'expectedCollection' => 'posts',
                'expectedAction'     => 'create',
                'expectedUserId'     => 'user-uuid',
            ],
            'limit is capped at 100 when value exceeds maximum' => [
                'params'             => ['limit' => '999'],
                'expectedLimit'      => 100,
                'expectedOffset'     => 0,
                'expectedCollection' => null,
                'expectedAction'     => null,
                'expectedUserId'     => null,
            ],
            'empty string filters are treated as null' => [
                'params'             => ['collection' => '', 'action' => ''],
                'expectedLimit'      => 25,
                'expectedOffset'     => 0,
                'expectedCollection' => null,
                'expectedAction'     => null,
                'expectedUserId'     => null,
            ],
        ];
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    /**
     * Test that list() returns HTTP 200 with the standard data/meta envelope.
     */
    public function testListReturnsActivityCollectionForAdminUser(): void
    {
        $this->listRepository->method('findPaginated')->willReturn([$this->makeActivity()]);
        $this->listRepository->method('countAll')->willReturn(1);

        $response = $this->class->list(Request::create('/activity', 'GET'));
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    /**
     * Test that list() maps each Activity entity to its DTO array representation.
     */
    public function testListMapsEachActivityToArray(): void
    {
        $activity = $this->makeActivity('create', 'posts', '1');

        $this->listRepository->method('findPaginated')
                             ->willReturn([$activity]);

        $this->listRepository->method('countAll')
                             ->willReturn(1);

        $result = $this->class->list(Request::create('/activity', 'GET'))
                              ->getContent();

        $body = json_decode($result, true);

        $this->assertCount(1, $body['data']);
        $this->assertSame((string) $activity->getId(), $body['data'][0]['id']);
        $this->assertSame('create', $body['data'][0]['action']);
        $this->assertSame('posts', $body['data'][0]['collection']);
        $this->assertSame('1', $body['data'][0]['item']);
    }

    /**
     * Test that list() sets total_count from the repository count and filter_count from the result set size.
     */
    public function testListMetaReflectsTotalAndFilterCount(): void
    {
        $this->listRepository->method('findPaginated')->willReturn([$this->makeActivity(), $this->makeActivity()]);
        $this->listRepository->method('countAll')->willReturn(50);

        $body = json_decode($this->class->list(Request::create('/activity', 'GET'))->getContent(), true);

        $this->assertSame(50, $body['meta']['total_count']);
        $this->assertSame(2, $body['meta']['filter_count']);
    }

    /**
     * Test that list() returns an empty data array and zero counts when no records exist.
     */
    public function testListReturnsEmptyDataWhenNoRecordsExist(): void
    {
        $this->listRepository->method('findPaginated')->willReturn([]);
        $this->listRepository->method('countAll')->willReturn(0);

        $body = json_decode($this->class->list(Request::create('/activity', 'GET'))->getContent(), true);

        $this->assertSame([], $body['data']);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    // ── Query-parameter forwarding ────────────────────────────────────────────

    /**
     * Test that list() forwards query parameters to the repository with correct values.
     *
     * Asserts at the repository layer rather than the query object, exercising
     * the full controller → handler → repository call chain.
     *
     * @dataProvider queryParameterProvider
     *
     * @param array<string, string> $params
     */
    public function testListForwardsQueryParametersToRepository(
        array   $params,
        int     $expectedLimit,
        int     $expectedOffset,
        ?string $expectedCollection,
        ?string $expectedAction,
        ?string $expectedUserId,
    ): void {
        $this->listRepository
            ->expects($this->once())
            ->method('findPaginated')
            ->with($expectedLimit, $expectedOffset, $expectedCollection, $expectedAction, $expectedUserId)
            ->willReturn([]);

        $this->listRepository->method('countAll')->willReturn(0);

        $this->class->list(Request::create('/activity', 'GET', $params));
    }

    // ── Guard failures ────────────────────────────────────────────────────────

    /**
     * Test that list() propagates UnauthorizedException when no authentication token exists.
     */
    public function testListThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('This endpoint requires authentication.');

        $this->buildControllerWithUnauthenticatedGuard()
             ->list(Request::create('/activity', 'GET'));
    }

    /**
     * Test that list() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testListThrowsWhenClientIsNotAuthorized(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()
             ->list(Request::create('/activity', 'GET'));
    }

    // ── Access control ────────────────────────────────────────────────────────

    /**
     * Test that list() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testListThrowsWhenUserLacksAdminRole(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->buildControllerWithAccessDenied()
             ->list(Request::create('/activity', 'GET'));
    }
}
