<?php

declare(strict_types=1);

namespace App\Extensions\Application\DTO\Tests;

use App\Extensions\Application\DTO\ExtensionDto;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionDto::class)]
final class ExtensionDtoBaseTest extends ExtensionDtoTest
{
    public function testDtoCanBeInstantiated(): void
    {
        $dto = new ExtensionDto(
            id:          'id',
            name:        'name',
            type:        'hook',
            version:     '1.0.0',
            enabled:     false,
            description: null,
            meta:        null,
            createdAt:   '2024-01-01T00:00:00+00:00',
            updatedAt:   '2024-01-01T00:00:00+00:00',
        );

        $this->assertInstanceOf(ExtensionDto::class, $dto);
    }
}
