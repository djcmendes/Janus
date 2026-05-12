<?php

/**
 * @file VersionServiceBaseTest.php
 *
 * Tests for VersionService construction and interface compliance.
 *
 * @package App\Versions\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Service\Tests;

use App\Versions\Domain\Service\VersionService;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that VersionService is constructed correctly with the DBAL connection.
 */
#[CoversClass(VersionService::class)]
final class VersionServiceBaseTest extends VersionServiceTest
{
    /**
     * Test that the SUT is an instance of VersionService.
     */
    public function testIsInstanceOfVersionService(): void
    {
        $this->assertInstanceOf(VersionService::class, $this->class);
    }
}
