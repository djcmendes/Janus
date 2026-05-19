<?php

/**
 * @file DeploymentSetLogTest.php
 *
 * Tests for Deployment::setLog().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\Deployment;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setLog() stores a log string and returns fluent self.
 */
#[CoversClass(className: Deployment::class)]
final class DeploymentSetLogTest extends DeploymentTest
{
    /**
     * Test that setLog() stores the given log text.
     */
    public function testSetLogStoresLog(): void
    {
        $this->class->setLog('[HTTP 200] success');
        $this->assertSame('[HTTP 200] success', $this->class->getLog());
    }

    /**
     * Test that setLog() can clear the log by passing null.
     */
    public function testSetLogAcceptsNull(): void
    {
        $this->class->setLog('[HTTP 200] success');
        $this->class->setLog(null);
        $this->assertNull($this->class->getLog());
    }

    /**
     * Test that setLog() returns fluent self.
     */
    public function testSetLogReturnsSelf(): void
    {
        $result = $this->class->setLog('[HTTP 500] error');
        $this->assertSame($this->class, $result);
    }
}
