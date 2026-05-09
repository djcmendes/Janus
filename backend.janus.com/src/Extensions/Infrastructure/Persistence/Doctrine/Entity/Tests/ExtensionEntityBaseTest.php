<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExtensionEntity::class)]
final class ExtensionEntityBaseTest extends ExtensionEntityTest
{
    public function testGetIdReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(id: 'cccccccc-0000-7000-8000-000000000003');

        $this->assertSame('cccccccc-0000-7000-8000-000000000003', $entity->getId());
    }

    public function testSetIdReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setId('id-string'));
    }

    public function testGetNameReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(name: 'my-panel');

        $this->assertSame('my-panel', $entity->getName());
    }

    public function testSetNameReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setName('name'));
    }

    public function testGetTypeReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(type: ExtensionType::PANEL);

        $this->assertSame(ExtensionType::PANEL, $entity->getType());
    }

    public function testSetTypeReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setType(ExtensionType::MODULE));
    }

    public function testGetVersionReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(version: '3.0.1');

        $this->assertSame('3.0.1', $entity->getVersion());
    }

    public function testSetVersionReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setVersion('1.0.0'));
    }

    public function testIsEnabledReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(enabled: false);

        $this->assertFalse($entity->isEnabled());
    }

    public function testSetEnabledReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setEnabled(true));
    }

    public function testGetDescriptionReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(description: 'Desc text');

        $this->assertSame('Desc text', $entity->getDescription());
    }

    public function testGetDescriptionReturnsNull(): void
    {
        $entity = $this->makeEntity(description: null);

        $this->assertNull($entity->getDescription());
    }

    public function testSetDescriptionReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setDescription(null));
    }

    public function testGetMetaReturnsStoredValue(): void
    {
        $entity = $this->makeEntity(meta: ['entry' => 'main.js']);

        $this->assertSame(['entry' => 'main.js'], $entity->getMeta());
    }

    public function testGetMetaReturnsNull(): void
    {
        $entity = $this->makeEntity(meta: null);

        $this->assertNull($entity->getMeta());
    }

    public function testSetMetaReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setMeta(null));
    }

    public function testGetCreatedAtReturnsStoredValue(): void
    {
        $ts     = new \DateTimeImmutable('2023-05-10T10:00:00Z');
        $entity = $this->makeEntity(createdAt: $ts);

        $this->assertSame($ts, $entity->getCreatedAt());
    }

    public function testSetCreatedAtReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setCreatedAt(new \DateTimeImmutable()));
    }

    public function testGetUpdatedAtReturnsStoredValue(): void
    {
        $ts     = new \DateTimeImmutable('2024-08-01T00:00:00Z');
        $entity = $this->makeEntity(updatedAt: $ts);

        $this->assertSame($ts, $entity->getUpdatedAt());
    }

    public function testSetUpdatedAtReturnsFluent(): void
    {
        $entity = new ExtensionEntity();

        $this->assertSame($entity, $entity->setUpdatedAt(new \DateTimeImmutable()));
    }
}
