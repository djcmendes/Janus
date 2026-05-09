<?php

/**
 * @file DeploymentProviderSetNameTest.php
 *
 * Tests for DeploymentProvider::setName().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setName() updates the name, touches updatedAt, and returns fluent self.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderSetNameTest extends DeploymentProviderTest
{
    /**
     * Test that setName() updates the name.
     */
    public function testSetNameUpdatesName(): void
    {
        $this->class->setName('Vercel Production');
        $this->assertSame('Vercel Production', $this->class->getName());
    }

    /**
     * Test that setName() sets updatedAt to approximately now.
     */
    public function testSetNameTouchesUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->setName('New Name');
        $after = new \DateTimeImmutable();

        $this->assertNotNull($this->class->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->class->getUpdatedAt());
    }

    /**
     * Test that setName() returns fluent self.
     */
    public function testSetNameReturnsSelf(): void
    {
        $result = $this->class->setName('New Name');
        $this->assertSame($this->class, $result);
    }
}
