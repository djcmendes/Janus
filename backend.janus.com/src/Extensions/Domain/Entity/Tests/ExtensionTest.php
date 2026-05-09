<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use PHPUnit\Framework\TestCase;

abstract class ExtensionTest extends TestCase
{
    protected function makeExtension(
        string        $name        = 'my-plugin',
        ExtensionType $type        = ExtensionType::HOOK,
        string        $version     = '1.0.0',
        bool          $enabled     = false,
        ?string       $description = null,
        ?array        $meta        = null,
    ): Extension {
        return new Extension($name, $type, $version, $enabled, $description, $meta);
    }

    protected function makeReconstituted(
        string        $id          = 'aaaaaaaa-0000-7000-8000-000000000001',
        string        $name        = 'my-plugin',
        ExtensionType $type        = ExtensionType::HOOK,
        string        $version     = '1.0.0',
        bool          $enabled     = true,
        ?string       $description = 'A hook extension',
        ?array        $meta        = ['entry' => 'index.js'],
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): Extension {
        return Extension::reconstitute(
            id:          $id,
            name:        $name,
            type:        $type,
            version:     $version,
            enabled:     $enabled,
            description: $description,
            meta:        $meta,
            createdAt:   $createdAt ?? new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:   $updatedAt ?? new \DateTimeImmutable('2024-06-01T00:00:00Z'),
        );
    }
}
