<?php

/**
 * @file UtilsControllerSortTest.php
 *
 * Tests for UtilsController::sort().
 *
 * @package App\Utils\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Utils\Presentation\Controller\UtilsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies sort() reorders items, rejects bad input, and enforces authorization.
 */
#[CoversClass(className: UtilsController::class)]
#[CoversMethod(UtilsController::class, 'sort')]
final class UtilsControllerSortTest extends UtilsControllerTest
{
    public function testSortReturns200WithUpdatedCount(): void
    {
        $meta = $this->makeCollectionMetaWithSortField('sort');
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $request    = $this->jsonRequest(['items' => [['id' => 'uuid-1', 'sort' => 1]]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testSortResponseHasDataKey(): void
    {
        $meta = $this->makeCollectionMetaWithSortField('sort');
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $request    = $this->jsonRequest(['items' => [['id' => 'uuid-1', 'sort' => 1]]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testSortResponseDataHasUpdatedKey(): void
    {
        $meta = $this->makeCollectionMetaWithSortField('sort');
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $items      = [['id' => 'uuid-1', 'sort' => 1], ['id' => 'uuid-2', 'sort' => 2]];
        $request    = $this->jsonRequest(['items' => $items]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);
        $body       = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('updated', $body['data']);
        $this->assertSame(2, $body['data']['updated']);
    }

    public function testSortReturns404WhenCollectionNotFound(): void
    {
        $this->collectionRepository->method('findByName')->willReturn(null);

        $request    = $this->jsonRequest(['items' => [['id' => 'uuid-1', 'sort' => 1]]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('unknown', $request, $this->connection, $this->collectionRepository);

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    public function testSortReturns422WhenNoSortFieldConfigured(): void
    {
        $meta = $this->makeCollectionMetaWithSortField(null);
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $request    = $this->jsonRequest(['items' => [['id' => 'uuid-1', 'sort' => 1]]]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NO_SORT_FIELD', $body['errors'][0]['extensions']['code']);
    }

    public function testSortReturns422WhenItemsArrayIsEmpty(): void
    {
        $meta = $this->makeCollectionMetaWithSortField('sort');
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $request    = $this->jsonRequest(['items' => []]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testSortReturns422WhenItemsKeyMissing(): void
    {
        $meta = $this->makeCollectionMetaWithSortField('sort');
        $this->collectionRepository->method('findByName')->willReturn($meta);

        $request    = $this->jsonRequest([]);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->sort('articles', $request, $this->connection, $this->collectionRepository);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testSortThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = $this->jsonRequest(['items' => [['id' => 'uuid-1', 'sort' => 1]]]);
        $this->class->sort('articles', $request, $this->connection, $this->collectionRepository);
    }

    public function testSortThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->sort('articles', $this->jsonRequest([]), $this->connection, $this->collectionRepository);
    }
}
