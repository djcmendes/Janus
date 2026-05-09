<?php

/**
 * @file DeploymentProviderSetIsActiveTest.php
 *
 * Tests for DeploymentProvider::setIsActive().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setIsActive() toggles the active flag, touches updatedAt, and returns fluent self.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderSetIsActiveTest extends DeploymentProviderTest
{
    /**
     * Test that setIsActive(false) deactivates the provider.
     */
    public function testSetIsActiveCanDeactivate(): void
    {
        $this->class->setIsActive(false);
        $this->assertFalse($this->class->isActive());
    }

    /**
     * Test that setIsActive(true) activates the provider.
     */
    public function testSetIsActiveCanActivate(): void
    {
        $this->class->setIsActive(false);
        $this->class->setIsActive(true);
        $this->assertTrue($this->class->isActive());
    }

    /**
     * Test that setIsActive() touches updatedAt.
     */
    public function testSetIsActiveTouchesUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->setIsActive(false);
        $after = new \DateTimeImmutable();

        $this->assertNotNull($this->class->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->class->getUpdatedAt());
    }

    /**
     * Test that setIsActive() returns fluent self.
     */
    public function testSetIsActiveReturnsSelf(): void
    {
        $result = $this->class->setIsActive(false);
        $this->assertSame($this->class, $result);
    }
}
