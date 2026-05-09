<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use App\Extensions\Domain\Enum\ExtensionType;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Extensions\Domain\Entity\Extension;

#[CoversClass(Extension::class)]
final class ExtensionReconstituteTest extends ExtensionTest
{
    public function testReconstitutePreservesId(): void
    {
        $ext = $this->makeReconstituted(id: 'bbbbbbbb-0000-7000-8000-000000000002');

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $ext->getId());
    }

    public function testReconstitutePreservesName(): void
    {
        $ext = $this->makeReconstituted(name: 'display-widget');

        $this->assertSame('display-widget', $ext->getName());
    }

    public function testReconstitutePreservesType(): void
    {
        $ext = $this->makeReconstituted(type: ExtensionType::DISPLAY);

        $this->assertSame(ExtensionType::DISPLAY, $ext->getType());
    }

    public function testReconstitutePreservesVersion(): void
    {
        $ext = $this->makeReconstituted(version: '3.1.2');

        $this->assertSame('3.1.2', $ext->getVersion());
    }

    public function testReconstitutePreservesEnabled(): void
    {
        $ext = $this->makeReconstituted(enabled: true);

        $this->assertTrue($ext->isEnabled());
    }

    public function testReconstitutePreservesEnabledFalse(): void
    {
        $ext = $this->makeReconstituted(enabled: false);

        $this->assertFalse($ext->isEnabled());
    }

    public function testReconstitutePreservesDescription(): void
    {
        $ext = $this->makeReconstituted(description: 'A display component');

        $this->assertSame('A display component', $ext->getDescription());
    }

    public function testReconstitutePreservesNullDescription(): void
    {
        $ext = $this->makeReconstituted(description: null);

        $this->assertNull($ext->getDescription());
    }

    public function testReconstitutePreservesMeta(): void
    {
        $ext = $this->makeReconstituted(meta: ['entry' => 'src/index.ts']);

        $this->assertSame(['entry' => 'src/index.ts'], $ext->getMeta());
    }

    public function testReconstitutePreservesNullMeta(): void
    {
        $ext = $this->makeReconstituted(meta: null);

        $this->assertNull($ext->getMeta());
    }

    public function testReconstitutePreservesCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2023-03-15T08:00:00Z');
        $ext       = $this->makeReconstituted(createdAt: $createdAt);

        $this->assertSame($createdAt, $ext->getCreatedAt());
    }

    public function testReconstitutePreservesUpdatedAt(): void
    {
        $updatedAt = new \DateTimeImmutable('2024-07-20T12:00:00Z');
        $ext       = $this->makeReconstituted(updatedAt: $updatedAt);

        $this->assertSame($updatedAt, $ext->getUpdatedAt());
    }
}
