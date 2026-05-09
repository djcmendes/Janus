<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\Query\Handler\GetFieldsByCollectionHandler;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class GetFieldsByCollectionHandlerTest extends TestCase
{
    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $repository;
    protected GetFieldsByCollectionHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->handler    = new GetFieldsByCollectionHandler(repository: $this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->handler);
    }

    protected function makeFieldMeta(string $collection = 'articles'): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: $collection,
            field:      'title',
            type:       FieldType::STRING,
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:  null,
        );
    }
}
