<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use PHPUnit\Framework\TestCase;

abstract class FieldMetaEntityTest extends TestCase
{
    protected function makeEntity(): FieldMetaEntity
    {
        return (new FieldMetaEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setCollection('articles')
            ->setField('title')
            ->setType(FieldType::STRING)
            ->setLabel(null)
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
}
