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
    /**
     * Data provider to rovide query-string inputs and the findPaginated() arguments
     * the repository is expected to receive after the real handler processes them.
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

    /**
     * Test that list() returns HTTP 200 with the standard data/meta envelope.
     */
    public function testListReturnsActivityCollectionForAdminUser(): void
    {
        $this->listRepository->method(constraint: 'findPaginated')
                             ->willReturn(value: [$this->makeActivity()]);

        $this->listRepository->method(constraint: 'countAll')
                             ->willReturn(value: 1);

        $response = $this->class->list(Request::create(uri: '/activity', method: 'GET'));
        $body     = json_decode(json: $response->getContent(), associative: true);

        $this->assertSame(expected: Response::HTTP_OK, actual: $response->getStatusCode());
        $this->assertArrayHasKey(key: 'data', array: $body);
        $this->assertArrayHasKey(key: 'meta', array: $body);
    }

    /**
     * Test that list() maps each Activity entity to its DTO array representation.
     */
    public function testListMapsEachActivityToArray(): void
    {
        $activity = $this->makeActivity(action: 'create', collection: 'posts', item: '1');

        $this->listRepository->method(constraint: 'findPaginated')
                             ->willReturn(value: [$activity]);

        $this->listRepository->method(constraint: 'countAll')
                             ->willReturn(value: 1);

        $result = $this->class->list(request: Request::create(uri: '/activity', method: 'GET'))
                              ->getContent();

        $body = json_decode(json: $result, associative: true);

        $this->assertCount(expectedCount: 1, haystack: $body['data']);
        $this->assertSame(expected: (string) $activity->getId(), actual: $body['data'][0]['id']);
        $this->assertSame(expected: 'create', actual: $body['data'][0]['action']);
        $this->assertSame(expected: 'posts', actual: $body['data'][0]['collection']);
        $this->assertSame(expected: '1', actual: $body['data'][0]['item']);
    }

    /**
     * Test that list() sets total_count from the repository count and filter_count from the result set size.
     */
    public function testListMetaReflectsTotalAndFilterCount(): void
    {
        $this->listRepository->method(constraint:'findPaginated')
                             ->willReturn(value: [
                                 $this->makeActivity(),
                                 $this->makeActivity()
                             ]);

        $this->listRepository->method(constraint: 'countAll')
                             ->willReturn(value: 50);

        $body = json_decode(
            json:        $this->class->list(Request::create(uri: '/activity', method: 'GET'))->getContent(),
            associative: true
        );

        $this->assertSame(expected: 50, actual: $body['meta']['total_count']);
        $this->assertSame(expected: 2, actual: $body['meta']['filter_count']);
    }

    /**
     * Test that list() returns an empty data array and zero counts when no records exist.
     */
    public function testListReturnsEmptyDataWhenNoRecordsExist(): void
    {
        $this->listRepository->method(constraint: 'findPaginated')
                             ->willReturn(value: []);

        $this->listRepository->method(constraint: 'countAll')
                             ->willReturn(value: 0);

        $body = json_decode(
            json:        $this->class->list(request: Request::create(uri: '/activity', method: 'GET'))->getContent(),
            associative: true
        );

        $this->assertSame(expected: [], actual: $body['data']);
        $this->assertSame(expected: 0,  actual: $body['meta']['total_count']);
        $this->assertSame(expected: 0,  actual: $body['meta']['filter_count']);
    }

    /**
     * Test that list() forwards query parameters to the repository with correct values.
     *
     * Asserts at the repository layer rather than the query object, exercising
     * the full controller → handler → repository call chain.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('queryParameterProvider')]
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
             ->method(constraint: 'findPaginated')
             ->with($expectedLimit, $expectedOffset, $expectedCollection, $expectedAction, $expectedUserId)
             ->willReturn(value: []);

        $this->listRepository->method(constraint: 'countAll')
                             ->willReturn(value: 0);

        $this->class->list(Request::create(uri: '/activity', method: 'GET', parameters: $params));
    }

    /**
     * Test that list() propagates UnauthorizedException when no authentication token exists.
     */
    public function testListThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(exception: UnauthorizedException::class);
        $this->expectExceptionMessage(message: 'This endpoint requires authentication.');

        $this->buildControllerWithUnauthenticatedGuard()
             ->list(request: Request::create(uri: '/activity', method: 'GET'));
    }

    /**
     * Test that list() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testListThrowsWhenClientIsNotAuthorized(): void
    {
        $this->expectException(exception: UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()
             ->list(request: Request::create(uri: '/activity', method: 'GET'));
    }

    /**
     * Test that list() throws AccessDeniedException when the user lacks ROLE_ADMIN.
     */
    public function testListThrowsWhenUserLacksAdminRole(): void
    {
        $this->expectException(exception: AccessDeniedException::class);

        $this->buildControllerWithAccessDenied()
             ->list(request: Request::create(uri: '/activity', method: 'GET'));
    }
}
