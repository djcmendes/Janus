<?php

/**
 * @file VersionServiceBaseTest.php
 *
 * Tests for VersionService construction and interface compliance.
 *
 * @package App\Versions\Infrastructure\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Service\Tests;

use App\Versions\Infrastructure\Service\VersionService;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionService is constructed correctly with the DBAL connection.
 */
#[CoversClass(className: VersionService::class)]
final class VersionServiceBaseTest extends VersionServiceTest
{
    public function testIsInstanceOfVersionService(): void
    {
        $this->assertInstanceOf(VersionService::class, $this->class);
    }
}
