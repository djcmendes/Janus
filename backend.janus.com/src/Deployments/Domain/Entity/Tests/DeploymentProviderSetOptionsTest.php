<?php

/**
 * @file DeploymentProviderSetOptionsTest.php
 *
 * Tests for DeploymentProvider::setOptions().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setOptions() stores options, touches updatedAt, and returns fluent self.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderSetOptionsTest extends DeploymentProviderTest
{
    /**
     * Test that setOptions() stores the given options array.
     */
    public function testSetOptionsStoresOptions(): void
    {
        $opts = ['headers' => ['X-Secret' => 'abc']];
        $this->class->setOptions($opts);
        $this->assertSame($opts, $this->class->getOptions());
    }

    /**
     * Test that setOptions() accepts null to clear options.
     */
    public function testSetOptionsAcceptsNull(): void
    {
        $this->class->setOptions(['foo' => 'bar']);
        $this->class->setOptions(null);
        $this->assertNull($this->class->getOptions());
    }

    /**
     * Test that setOptions() touches updatedAt.
     */
    public function testSetOptionsTouchesUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->setOptions(['key' => 'value']);
        $after = new \DateTimeImmutable();

        $this->assertNotNull($this->class->getUpdatedAt());
        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->class->getUpdatedAt());
    }

    /**
     * Test that setOptions() returns fluent self.
     */
    public function testSetOptionsReturnsSelf(): void
    {
        $result = $this->class->setOptions(null);
        $this->assertSame($this->class, $result);
    }
}
