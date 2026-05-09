<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler\Tests;

use App\Fields\Application\Query\Handler\GetFieldsHandler;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class GetFieldsHandlerTest extends TestCase
{
    /** @var MockObject&FieldMetaRepositoryInterface */
    protected MockObject $repository;
    protected GetFieldsHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->createMock(FieldMetaRepositoryInterface::class);
        $this->handler    = new GetFieldsHandler(repository: $this->repository);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->handler);
    }

    protected function makeFieldMeta(): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: 'articles',
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
