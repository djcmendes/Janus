<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use PHPUnit\Framework\TestCase;

abstract class FieldMetaMapperTest extends TestCase
{
    protected FieldMetaMapper $mapper;

    public function setUp(): void
    {
        $this->mapper = new FieldMetaMapper();
    }

    public function tearDown(): void
    {
        unset($this->mapper);
    }

    protected function makeEntity(): FieldMetaEntity
    {
        return (new FieldMetaEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setCollection('articles')
            ->setField('title')
            ->setType(FieldType::STRING)
            ->setLabel('Article Title')
            ->setNote(null)
            ->setRequired(false)
            ->setReadonly(false)
            ->setHidden(false)
            ->setSortOrder(0)
            ->setInterface(null)
            ->setOptions(null)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(null);
    }

    protected function makeDomain(): FieldMeta
    {
        return FieldMeta::reconstitute(
            id:         'aaaaaaaa-0000-7000-8000-000000000001',
            collection: 'articles',
            field:      'title',
            type:       FieldType::STRING,
            label:      'Article Title',
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
