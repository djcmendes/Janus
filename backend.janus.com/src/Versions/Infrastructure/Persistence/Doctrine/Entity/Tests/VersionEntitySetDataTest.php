<?php

/**
 * @file VersionEntitySetDataTest.php
 *
 * Tests for VersionEntity::setData().
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
 * Verifies that setData() stores the array and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setData')]
final class VersionEntitySetDataTest extends VersionEntityTest
{
    public function testSetDataStoresArray(): void
    {
        $data = ['title' => 'Updated', 'body' => 'Text'];
        $this->class->setData($data);

        $this->assertSame($data, $this->class->getData());
    }

    public function testSetDataReturnsStatic(): void
    {
        $result = $this->class->setData([]);
        $this->assertSame($this->class, $result);
    }
}
