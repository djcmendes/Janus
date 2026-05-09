<?php

declare(strict_types=1);

namespace App\Fields\Domain\Entity\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldMeta::class)]
final class FieldMetaReconstituteTest extends FieldMetaTest
{
    public function testReconstitutePreservesId(): void
    {
        $f = $this->makeReconstituted(id: 'bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $f->getId());
    }

    public function testReconstitutePreservesCollection(): void
    {
        $f = $this->makeReconstituted(collection: 'products');

        $this->assertSame('products', $f->getCollection());
    }

    public function testReconstitutePreservesField(): void
    {
        $f = $this->makeReconstituted(field: 'price');

        $this->assertSame('price', $f->getField());
    }

    public function testReconstitutePreservesType(): void
    {
        $f = $this->makeReconstituted(type: FieldType::DECIMAL);

        $this->assertSame(FieldType::DECIMAL, $f->getType());
    }

    public function testReconstitutePreservesLabel(): void
    {
        $f = $this->makeReconstituted(label: 'Product Price');

        $this->assertSame('Product Price', $f->getLabel());
    }

    public function testReconstitutePreservesNullLabel(): void
    {
        $f = $this->makeReconstituted(label: null);

        $this->assertNull($f->getLabel());
    }

    public function testReconstitutePreservesNote(): void
    {
        $f = $this->makeReconstituted(note: 'Price in USD');

        $this->assertSame('Price in USD', $f->getNote());
    }

    public function testReconstitutePreservesRequired(): void
    {
        $f = $this->makeReconstituted(required: true);

        $this->assertTrue($f->isRequired());
    }

    public function testReconstitutePreservesReadonly(): void
    {
        $f = $this->makeReconstituted(readonly: true);

        $this->assertTrue($f->isReadonly());
    }

    public function testReconstitutePreservesHidden(): void
    {
        $f = $this->makeReconstituted(hidden: true);

        $this->assertTrue($f->isHidden());
    }

    public function testReconstitutePreservesSortOrder(): void
    {
        $f = $this->makeReconstituted(sortOrder: 5);

        $this->assertSame(5, $f->getSortOrder());
    }

    public function testReconstitutePreservesInterface(): void
    {
        $f = $this->makeReconstituted(interface: 'input-text');

        $this->assertSame('input-text', $f->getInterface());
    }

    public function testReconstitutePreservesNullInterface(): void
    {
        $f = $this->makeReconstituted(interface: null);

        $this->assertNull($f->getInterface());
    }

    public function testReconstitutePreservesOptions(): void
    {
        $f = $this->makeReconstituted(options: ['max' => 100]);

        $this->assertSame(['max' => 100], $f->getOptions());
    }

    public function testReconstitutePreservesNullOptions(): void
    {
        $f = $this->makeReconstituted(options: null);

        $this->assertNull($f->getOptions());
    }

    public function testReconstitutePreservesCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2023-05-10T10:00:00Z');
        $f         = $this->makeReconstituted(createdAt: $createdAt);

        $this->assertSame($createdAt, $f->getCreatedAt());
    }

    public function testReconstitutePreservesNullUpdatedAt(): void
    {
        $f = $this->makeReconstituted(updatedAt: null);

        $this->assertNull($f->getUpdatedAt());
    }

    public function testReconstitutePreservesUpdatedAt(): void
    {
        $updatedAt = new \DateTimeImmutable('2024-08-15T14:00:00Z');
        $f         = $this->makeReconstituted(updatedAt: $updatedAt);

        $this->assertSame($updatedAt, $f->getUpdatedAt());
    }
}
