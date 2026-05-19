<?php

/**
 * @file SchemaSnapshotServiceTest.php
 *
 * Abstract base for SchemaSnapshotService test suites.
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use App\Relations\Domain\Repository\RelationRepositoryInterface;
use App\Schema\Domain\Service\SchemaSnapshotService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup and helpers for all SchemaSnapshotService test suites.
 *
 * Repository `findPaginated` stubs use willReturnCallback so individual tests
 * can override the return value by setting the corresponding property without
 * triggering PHPUnit's FIFO stub ordering.
 */
#[CoversClass(className: SchemaSnapshotService::class)]
abstract class SchemaSnapshotServiceTest extends TestCase
{
    /** @var MockObject&CollectionMetaRepositoryInterface */
    protected MockObject $collectionRepository;

    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $fieldRepository;

    /** @var MockObject&RelationRepositoryInterface */
    protected MockObject $relationRepository;

    /** @var SchemaSnapshotService */
    protected SchemaSnapshotService $class;

    /** @var array<mixed> Mutable return value for collectionRepository::findPaginated */
    protected array $collectionReturn = [];

    /** @var array<mixed> Mutable return value for fieldRepository::findPaginated */
    protected array $fieldReturn = [];

    /** @var array<mixed> Mutable return value for relationRepository::findPaginated */
    protected array $relationReturn = [];

    public function setUp(): void
    {
        $this->collectionReturn = [];
        $this->fieldReturn      = [];
        $this->relationReturn   = [];

        $this->collectionRepository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->fieldRepository      = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->relationRepository   = $this->createMock(RelationRepositoryInterface::class);

        $this->collectionRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->collectionReturn);
        $this->fieldRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->fieldReturn);
        $this->relationRepository->method('findPaginated')
            ->willReturnCallback(fn() => $this->relationReturn);

        $this->class = new SchemaSnapshotService(
            collectionRepository: $this->collectionRepository,
            fieldRepository:      $this->fieldRepository,
            relationRepository:   $this->relationRepository,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->collectionRepository,
            $this->fieldRepository,
            $this->relationRepository,
            $this->class,
        );
    }
}
