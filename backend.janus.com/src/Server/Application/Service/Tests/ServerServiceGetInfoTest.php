<?php

/**
 * @file ServerServiceGetInfoTest.php
 *
 * Tests for ServerService::getInfo().
 *
 * @package App\Server\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Application\Service\Tests;

use App\Server\Application\Service\ServerService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that getInfo() returns the expected shape and values.
 */
#[CoversClass(className: ServerService::class)]
#[CoversMethod(ServerService::class, 'getInfo')]
final class ServerServiceGetInfoTest extends ServerServiceTest
{
    public function testGetInfoReturnsArray(): void
    {
        $this->assertIsArray($this->class->getInfo());
    }

    public function testGetInfoHasProjectNameKey(): void
    {
        $this->assertArrayHasKey('project_name', $this->class->getInfo());
    }

    public function testGetInfoHasVersionKey(): void
    {
        $this->assertArrayHasKey('version', $this->class->getInfo());
    }

    public function testGetInfoHasPhpVersionKey(): void
    {
        $this->assertArrayHasKey('php_version', $this->class->getInfo());
    }

    public function testGetInfoHasMaxUploadSizeKey(): void
    {
        $this->assertArrayHasKey('max_upload_size', $this->class->getInfo());
    }

    public function testGetInfoHasRateLimiterEnabledKey(): void
    {
        $this->assertArrayHasKey('rate_limiter_enabled', $this->class->getInfo());
    }

    public function testGetInfoProjectNameIsJanus(): void
    {
        $this->assertSame('Janus', $this->class->getInfo()['project_name']);
    }

    public function testGetInfoPhpVersionMatchesRuntime(): void
    {
        $this->assertSame(PHP_VERSION, $this->class->getInfo()['php_version']);
    }
}
