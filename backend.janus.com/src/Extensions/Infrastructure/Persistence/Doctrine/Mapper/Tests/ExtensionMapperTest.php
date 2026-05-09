<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use PHPUnit\Framework\TestCase;

abstract class ExtensionMapperTest extends TestCase
{
    protected ExtensionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ExtensionMapper();
    }

    protected function makeEntity(
        string        $id          = 'aaaaaaaa-0000-7000-8000-000000000001',
        string        $name        = 'my-hook',
        ExtensionType $type        = ExtensionType::HOOK,
        string        $version     = '1.0.0',
        bool          $enabled     = true,
        ?string       $description = 'A hook extension',
        ?array        $meta        = ['entry' => 'index.js'],
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): ExtensionEntity {
        return (new ExtensionEntity())
            ->setId($id)
            ->setName($name)
            ->setType($type)
            ->setVersion($version)
            ->setEnabled($enabled)
            ->setDescription($description)
            ->setMeta($meta)
            ->setCreatedAt($createdAt ?? new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt($updatedAt ?? new \DateTimeImmutable('2024-06-01T00:00:00Z'));
    }

    protected function makeDomain(
        string        $id          = 'aaaaaaaa-0000-7000-8000-000000000001',
        string        $name        = 'my-hook',
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
