<?php

/**
 * @file DeploymentProviderSetUrlTest.php
 *
 * Tests for DeploymentProvider::setUrl().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setUrl() updates the URL, touches updatedAt, and returns fluent self.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderSetUrlTest extends DeploymentProviderTest
{
    /**
     * Test that setUrl() updates the URL.
     */
    public function testSetUrlUpdatesUrl(): void
    {
        $this->class->setUrl('https://new-hook.example.com/deploy');
        $this->assertSame('https://new-hook.example.com/deploy', $this->class->getUrl());
    }

    /**
     * Test that setUrl() sets updatedAt to approximately now.
     */
    public function testSetUrlTouchesUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->setUrl('https://new-hook.example.com/deploy');
        $after = new \DateTimeImmutable();

        $this->assertNotNull($this->class->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->class->getUpdatedAt());
    }

    /**
     * Test that setUrl() returns fluent self.
     */
    public function testSetUrlReturnsSelf(): void
    {
        $result = $this->class->setUrl('https://other.example.com/hook');
        $this->assertSame($this->class, $result);
    }
}
