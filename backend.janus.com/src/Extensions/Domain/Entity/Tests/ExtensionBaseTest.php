<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity\Tests;

use App\Extensions\Domain\Enum\ExtensionType;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Extensions\Domain\Entity\Extension;

#[CoversClass(Extension::class)]
final class ExtensionBaseTest extends ExtensionTest
{
    public function testIdIsUuidV7Format(): void
    {
        $ext = $this->makeExtension();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $ext->getId(),
        );
    }

    public function testNameIsStored(): void
    {
        $ext = $this->makeExtension(name: 'test-hook');

        $this->assertSame('test-hook', $ext->getName());
    }

    public function testTypeIsStored(): void
    {
        $ext = $this->makeExtension(type: ExtensionType::MODULE);

        $this->assertSame(ExtensionType::MODULE, $ext->getType());
    }

    public function testVersionIsStored(): void
    {
        $ext = $this->makeExtension(version: '2.3.4');

        $this->assertSame('2.3.4', $ext->getVersion());
    }

    public function testDefaultEnabledIsFalse(): void
    {
        $ext = $this->makeExtension();

        $this->assertFalse($ext->isEnabled());
    }

    public function testEnabledTrueIsStored(): void
    {
        $ext = $this->makeExtension(enabled: true);

        $this->assertTrue($ext->isEnabled());
    }

    public function testDescriptionIsNullByDefault(): void
    {
        $ext = $this->makeExtension();

        $this->assertNull($ext->getDescription());
    }

    public function testDescriptionIsStored(): void
    {
        $ext = $this->makeExtension(description: 'My hook');

        $this->assertSame('My hook', $ext->getDescription());
    }

    public function testMetaIsNullByDefault(): void
    {
        $ext = $this->makeExtension();

        $this->assertNull($ext->getMeta());
    }

    public function testMetaIsStored(): void
    {
        $ext = $this->makeExtension(meta: ['entry' => 'index.js']);

        $this->assertSame(['entry' => 'index.js'], $ext->getMeta());
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $before = new \DateTimeImmutable();
        $ext    = $this->makeExtension();
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $ext->getCreatedAt());
        $this->assertLessThanOrEqual($after, $ext->getCreatedAt());
    }

    public function testUpdatedAtIsSetOnConstruction(): void
    {
        $before = new \DateTimeImmutable();
        $ext    = $this->makeExtension();
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $ext->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $ext->getUpdatedAt());
    }

    public function testTwoInstancesHaveDifferentIds(): void
    {
        $a = $this->makeExtension();
        $b = $this->makeExtension();

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
