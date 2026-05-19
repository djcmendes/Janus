<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldMetaEntity::class)]
final class FieldMetaEntityBaseTest extends FieldMetaEntityTest
{
    public function testGetIdReturnsStoredValue(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $entity->getId());
    }

    public function testGetCollectionReturnsStoredValue(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('articles', $entity->getCollection());
    }

    public function testGetFieldReturnsStoredValue(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame('title', $entity->getField());
    }

    public function testGetTypeReturnsStoredValue(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame(FieldType::STRING, $entity->getType());
    }

    public function testGetLabelReturnsNull(): void
    {
        $entity = $this->makeEntity();

        $this->assertNull($entity->getLabel());
    }

    public function testSetLabelStoresValue(): void
    {
        $entity = (new FieldMetaEntity())->setLabel('My Label');

        $this->assertSame('My Label', $entity->getLabel());
    }

    public function testGetNoteReturnsNull(): void
    {
        $entity = $this->makeEntity();

        $this->assertNull($entity->getNote());
    }

    public function testIsRequiredReturnsFalse(): void
    {
        $entity = $this->makeEntity();

        $this->assertFalse($entity->isRequired());
    }

    public function testIsReadonlyReturnsFalse(): void
    {
        $entity = $this->makeEntity();

        $this->assertFalse($entity->isReadonly());
    }

    public function testIsHiddenReturnsFalse(): void
    {
        $entity = $this->makeEntity();

        $this->assertFalse($entity->isHidden());
    }

    public function testGetSortOrderReturnsZero(): void
    {
        $entity = $this->makeEntity();

        $this->assertSame(0, $entity->getSortOrder());
    }

    public function testGetInterfaceReturnsNull(): void
    {
        $entity = $this->makeEntity();

        $this->assertNull($entity->getInterface());
    }

    public function testGetOptionsReturnsNull(): void
    {
        $entity = $this->makeEntity();

        $this->assertNull($entity->getOptions());
    }

    public function testSetOptionsStoresArray(): void
    {
        $entity = (new FieldMetaEntity())->setOptions(['key' => 'val']);

        $this->assertSame(['key' => 'val'], $entity->getOptions());
    }

    public function testGetUpdatedAtReturnsNull(): void
    {
        $entity = $this->makeEntity();

        $this->assertNull($entity->getUpdatedAt());
    }

    public function testSetUpdatedAtStoresValue(): void
    {
        $dt     = new \DateTimeImmutable('2024-06-01T00:00:00Z');
        $entity = (new FieldMetaEntity())->setUpdatedAt($dt);

        $this->assertSame($dt, $entity->getUpdatedAt());
    }

    public function testSetterReturnsSelf(): void
    {
        $entity = new FieldMetaEntity();

        $this->assertSame($entity, $entity->setId('some-id'));
    }

    public function testGetCreatedAtReturnsStoredValue(): void
    {
        $entity = $this->makeEntity();

        $this->assertEquals(new \DateTimeImmutable('2024-01-01T00:00:00Z'), $entity->getCreatedAt());
    }
}
