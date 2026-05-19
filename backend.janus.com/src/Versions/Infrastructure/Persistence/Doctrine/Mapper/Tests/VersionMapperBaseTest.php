<?php

/**
 * @file VersionMapperBaseTest.php
 *
 * Tests for VersionMapper construction.
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionMapper can be instantiated without dependencies.
 */
#[CoversClass(className: VersionMapper::class)]
final class VersionMapperBaseTest extends VersionMapperTest
{
    public function testIsInstanceOfVersionMapper(): void
    {
        $this->assertInstanceOf(VersionMapper::class, $this->class);
    }
}
