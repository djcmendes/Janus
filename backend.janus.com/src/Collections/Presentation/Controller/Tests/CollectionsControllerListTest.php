<?php

/**
 * @file CollectionsControllerListTest.php
 *
 * Tests for CollectionsController::list().
 *
 * @package App\Collections\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\Controller\Tests;

use App\Collections\Presentation\Controller\CollectionsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Covers: response envelope shape, pagination forwarding, limit capping,
 * empty-list handling, and guard/auth failures.
 */
#[CoversClass(className: CollectionsController::class)]
#[CoversMethod(CollectionsController::class, 'list')]
final class CollectionsControllerListTest extends CollectionsControllerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that list() returns HTTP 200 with a data array and meta block.
     */
    public function testListReturns200WithDataAndMeta(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $response = $this->class->list(new Request());
        $body     = json_decode($response->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('meta', $body);
    }

    /**
     * Test that list() includes mapped collection fields in the data array.
     */
    public function testListDataContainsMappedCollectionFields(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findPaginated')->willReturn([$collection]);
        $this->repository->method('count')->willReturn(1);

        $body = json_decode($this->class->list(new Request())->getContent(), true);

        $this->assertCount(1, $body['data']);
        $this->assertArrayHasKey('id', $body['data'][0]);
        $this->assertSame('articles', $body['data'][0]['collection']);
        $this->assertSame('Articles', $body['data'][0]['label']);
    }

    /**
     * Test that meta contains total_count from the repository and filter_count from the page.
     */
    public function testListMetaContainsTotalAndFilterCount(): void
    {
        $collection = $this->makeCollectionMeta();
        $this->repository->method('findPaginated')->willReturn([$collection]);
        $this->repository->method('count')->willReturn(5);

        $body = json_decode($this->class->list(new Request())->getContent(), true);

        $this->assertSame(5, $body['meta']['total_count']);
        $this->assertSame(1, $body['meta']['filter_count']);
    }

    /**
     * Test that list() uses limit=25 and offset=0 when no query params are present.
     */
    public function testListUsesDefaultLimitAndOffset(): void
    {
        $this->repository->expects($this->once())
                         ->method('findPaginated')
                         ->with(25, 0)
                         ->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $this->class->list(new Request());
    }

    /**
     * Test that list() caps the limit at 100 even when a higher value is requested.
     */
    public function testListCapsLimitAt100(): void
    {
        $this->repository->expects($this->once())
                         ->method('findPaginated')
                         ->with(100, 0)
                         ->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $this->class->list(new Request(['limit' => '200', 'offset' => '0']));
    }

    /**
     * Test that list() forwards custom limit and offset values to the handler.
     */
    public function testListWithCustomPagination(): void
    {
        $this->repository->expects($this->once())
                         ->method('findPaginated')
                         ->with(10, 20)
                         ->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $this->class->list(new Request(['limit' => '10', 'offset' => '20']));
    }

    /**
     * Test that list() returns an empty data array and zero totals when no collections exist.
     */
    public function testListReturnsEmptyDataArrayWhenNoCollections(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('count')->willReturn(0);

        $body = json_decode($this->class->list(new Request())->getContent(), true);

        $this->assertSame([], $body['data']);
        $this->assertSame(0, $body['meta']['total_count']);
        $this->assertSame(0, $body['meta']['filter_count']);
    }

    // Guard / auth failures ────────────────────────────────────────

    /**
     * Test that list() propagates UnauthorizedException when no authentication token exists.
     */
    public function testListThrowsWhenAuthenticationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthenticatedGuard()->list(new Request());
    }

    /**
     * Test that list() propagates UnauthorizedException when the client type is not allowed.
     */
    public function testListThrowsWhenClientNotAllowed(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->buildControllerWithUnauthorizedClient()->list(new Request());
    }
}
