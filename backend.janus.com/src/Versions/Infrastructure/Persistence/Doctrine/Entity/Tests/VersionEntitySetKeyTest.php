<?php

/**
 * @file VersionEntitySetKeyTest.php
 *
 * Tests for VersionEntity::setKey().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that setKey() stores the label and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setKey')]
final class VersionEntitySetKeyTest extends VersionEntityTest
{
    public function testSetKeyStoresValue(): void
    {
        $this->class->setKey('draft');
        $this->assertSame('draft', $this->class->getKey());
    }

    public function testSetKeyReturnsStatic(): void
    {
        $result = $this->class->setKey('x');
        $this->assertSame($this->class, $result);
    }
}
