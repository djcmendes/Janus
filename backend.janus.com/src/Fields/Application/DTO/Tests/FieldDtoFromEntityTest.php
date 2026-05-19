<?php

declare(strict_types=1);

namespace App\Fields\Application\DTO\Tests;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldDto::class)]
final class FieldDtoFromEntityTest extends FieldDtoTest
{
    public function testFromEntityMapsId(): void
    {
        $f   = $this->makeFieldMeta(id: 'cccccccc-0000-7000-8000-000000000001');
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('cccccccc-0000-7000-8000-000000000001', $dto->id);
    }

    public function testFromEntityMapsCollection(): void
    {
        $f   = $this->makeFieldMeta(collection: 'products');
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('products', $dto->collection);
    }

    public function testFromEntityMapsField(): void
    {
        $f   = $this->makeFieldMeta(field: 'price');
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('price', $dto->field);
    }

    public function testFromEntityMapsTypeValue(): void
    {
        $f   = $this->makeFieldMeta(type: FieldType::DECIMAL);
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('decimal', $dto->type);
    }

    public function testFromEntityMapsLabel(): void
    {
        $f   = $this->makeFieldMeta(label: 'My Label');
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('My Label', $dto->label);
    }

    public function testFromEntityMapsNullLabel(): void
    {
        $f   = $this->makeFieldMeta(label: null);
        $dto = FieldDto::fromEntity($f);

        $this->assertNull($dto->label);
    }

    public function testFromEntityMapsRequired(): void
    {
        $f   = $this->makeFieldMeta(required: true);
        $dto = FieldDto::fromEntity($f);

        $this->assertTrue($dto->required);
    }

    public function testFromEntityMapsReadonly(): void
    {
        $f   = $this->makeFieldMeta(readonly: true);
        $dto = FieldDto::fromEntity($f);

        $this->assertTrue($dto->readonly);
    }

    public function testFromEntityMapsHidden(): void
    {
        $f   = $this->makeFieldMeta(hidden: true);
        $dto = FieldDto::fromEntity($f);

        $this->assertTrue($dto->hidden);
    }

    public function testFromEntityMapsSortOrder(): void
    {
        $f   = $this->makeFieldMeta(sortOrder: 7);
        $dto = FieldDto::fromEntity($f);

        $this->assertSame(7, $dto->sortOrder);
    }

    public function testFromEntityMapsInterface(): void
    {
        $f   = $this->makeFieldMeta(interface: 'input-text');
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('input-text', $dto->interface);
    }

    public function testFromEntityMapsOptions(): void
    {
        $f   = $this->makeFieldMeta(options: ['max' => 99]);
        $dto = FieldDto::fromEntity($f);

        $this->assertSame(['max' => 99], $dto->options);
    }

    public function testFromEntityMapsCreatedAt(): void
    {
        $f   = $this->makeFieldMeta();
        $dto = FieldDto::fromEntity($f);

        $this->assertSame('2024-01-01T00:00:00+00:00', $dto->createdAt);
    }

    public function testFromEntityMapsNullUpdatedAt(): void
    {
        $f   = $this->makeFieldMeta(updatedAt: null);
        $dto = FieldDto::fromEntity($f);

        $this->assertNull($dto->updatedAt);
    }

    public function testFromEntityMapsUpdatedAt(): void
    {
        $updatedAt = new \DateTimeImmutable('2024-06-15T12:00:00Z');
        $f         = $this->makeFieldMeta(updatedAt: $updatedAt);
        $dto       = FieldDto::fromEntity($f);

        $this->assertSame('2024-06-15T12:00:00+00:00', $dto->updatedAt);
    }
}
