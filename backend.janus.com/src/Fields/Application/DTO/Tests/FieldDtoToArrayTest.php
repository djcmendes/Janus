<?php

declare(strict_types=1);

namespace App\Fields\Application\DTO\Tests;

use App\Fields\Application\DTO\FieldDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldDto::class)]
final class FieldDtoToArrayTest extends FieldDtoTest
{
    private function makeDto(): FieldDto
    {
        return FieldDto::fromEntity($this->makeFieldMeta(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            label:     'Title',
            sortOrder: 3,
        ));
    }

    public function testToArrayContainsId(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $arr['id']);
    }

    public function testToArrayContainsCollection(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertSame('articles', $arr['collection']);
    }

    public function testToArrayContainsField(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertSame('title', $arr['field']);
    }

    public function testToArrayContainsType(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertSame('string', $arr['type']);
    }

    public function testToArrayContainsSortAsSortKey(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertArrayHasKey('sort', $arr);
        $this->assertSame(3, $arr['sort']);
    }

    public function testToArrayContainsNullUpdatedAt(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertNull($arr['updated_at']);
    }

    public function testToArrayContainsCreatedAt(): void
    {
        $arr = $this->makeDto()->toArray();

        $this->assertSame('2024-01-01T00:00:00+00:00', $arr['created_at']);
    }

    public function testToArrayContainsAllExpectedKeys(): void
    {
        $arr  = $this->makeDto()->toArray();
        $keys = ['id', 'collection', 'field', 'type', 'label', 'note', 'required', 'readonly', 'hidden', 'sort', 'interface', 'options', 'created_at', 'updated_at'];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $arr);
        }
    }
}
