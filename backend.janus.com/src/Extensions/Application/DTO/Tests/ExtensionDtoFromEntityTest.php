<?php

declare(strict_types=1);

namespace App\Extensions\Application\DTO\Tests;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Domain\Enum\ExtensionType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: ExtensionDto::class)]
final class ExtensionDtoFromEntityTest extends ExtensionDtoTest
{
    public function testFromEntityReturnsDtoInstance(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension());

        $this->assertInstanceOf(ExtensionDto::class, $dto);
    }

    public function testFromEntityMapsId(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(id: 'bbbbbbbb-0000-7000-8000-000000000002'));

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $dto->id);
    }

    public function testFromEntityMapsName(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(name: 'display-component'));

        $this->assertSame('display-component', $dto->name);
    }

    public function testFromEntityMapsTypeAsString(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(type: ExtensionType::DISPLAY));

        $this->assertSame('display', $dto->type);
    }

    public function testFromEntityMapsVersion(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(version: '3.2.1'));

        $this->assertSame('3.2.1', $dto->version);
    }

    public function testFromEntityMapsEnabled(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(enabled: false));

        $this->assertFalse($dto->enabled);
    }

    public function testFromEntityMapsDescription(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(description: 'My display'));

        $this->assertSame('My display', $dto->description);
    }

    public function testFromEntityMapsNullDescription(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(description: null));

        $this->assertNull($dto->description);
    }

    public function testFromEntityMapsMeta(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(meta: ['entry' => 'dist/main.js']));

        $this->assertSame(['entry' => 'dist/main.js'], $dto->meta);
    }

    public function testFromEntityMapsNullMeta(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension(meta: null));

        $this->assertNull($dto->meta);
    }

    public function testFromEntityMapsCreatedAtAsAtomString(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension());

        $this->assertSame('2024-01-01T00:00:00+00:00', $dto->createdAt);
    }

    public function testFromEntityMapsUpdatedAtAsAtomString(): void
    {
        $dto = ExtensionDto::fromEntity($this->makeExtension());

        $this->assertSame('2024-06-01T00:00:00+00:00', $dto->updatedAt);
    }
}
