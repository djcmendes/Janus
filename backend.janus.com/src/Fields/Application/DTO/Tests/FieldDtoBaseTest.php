<?php

declare(strict_types=1);

namespace App\Fields\Application\DTO\Tests;

use App\Fields\Application\DTO\FieldDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FieldDto::class)]
final class FieldDtoBaseTest extends FieldDtoTest
{
    public function testConstructorStoresId(): void
    {
        $dto = new FieldDto(
            id:         'test-id',
            collection: 'articles',
            field:      'title',
            type:       'string',
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  '2024-01-01T00:00:00+00:00',
            updatedAt:  null,
        );

        $this->assertSame('test-id', $dto->id);
    }

    public function testConstructorStoresCollection(): void
    {
        $dto = new FieldDto(
            id:         'test-id',
            collection: 'posts',
            field:      'body',
            type:       'text',
            label:      null,
            note:       null,
            required:   false,
            readonly:   false,
            hidden:     false,
            sortOrder:  0,
            interface:  null,
            options:    null,
            createdAt:  '2024-01-01T00:00:00+00:00',
            updatedAt:  null,
        );

        $this->assertSame('posts', $dto->collection);
    }
}
