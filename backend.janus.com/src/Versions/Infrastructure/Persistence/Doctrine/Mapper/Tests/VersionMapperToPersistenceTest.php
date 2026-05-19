<?php

/**
 * @file VersionMapperToPersistenceTest.php
 *
 * Tests for VersionMapper::toPersistence().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that toPersistence() converts all domain Version fields to a correct VersionEntity.
 */
#[CoversClass(className: VersionMapper::class)]
#[CoversMethod(VersionMapper::class, 'toPersistence')]
final class VersionMapperToPersistenceTest extends VersionMapperTest
{
    /**
     * Test that toPersistence() returns a VersionEntity instance.
     */
    public function testToPersistenceReturnsVersionEntity(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertInstanceOf(VersionEntity::class, $result);
    }

    /**
     * Test that toPersistence() maps the UUID string to a Doctrine Uuid.
     */
    public function testToPersistenceMapsIdAsUuid(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame(self::FIXED_UUID, (string) $result->getId());
    }

    /**
     * Test that toPersistence() maps the collection field.
     */
    public function testToPersistenceMapsCollection(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('articles', $result->getCollection());
    }

    /**
     * Test that toPersistence() maps the key field.
     */
    public function testToPersistenceMapsKey(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame('main', $result->getKey());
    }

    /**
     * Test that toPersistence() maps the data array.
     */
    public function testToPersistenceMapsData(): void
    {
        $result = $this->class->toPersistence($this->makeDomain());

        $this->assertSame(['title' => 'Hello'], $result->getData());
    }

    /**
     * Test that a roundtrip toDomain(toPersistence(domain)) preserves the collection.
     */
    public function testRoundtripPreservesCollection(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $result = $this->class->toDomain($entity);

        $this->assertSame($domain->getCollection(), $result->getCollection());
    }

    /**
     * Test that a roundtrip toDomain(toPersistence(domain)) preserves the id.
     */
    public function testRoundtripPreservesId(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $result = $this->class->toDomain($entity);

        $this->assertSame($domain->getId(), $result->getId());
    }
}
