<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use PHPUnit\Framework\TestCase;

abstract class ExtensionEntityTest extends TestCase
{
    protected function makeEntity(
        string        $id          = 'aaaaaaaa-0000-7000-8000-000000000001',
        string        $name        = 'my-hook',
        ExtensionType $type        = ExtensionType::HOOK,
        string        $version     = '1.0.0',
        bool          $enabled     = true,
        ?string       $description = 'A hook',
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
}
