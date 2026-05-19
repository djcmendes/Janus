<?php

/**
 * @file ApplySchemaCommandBaseTest.php
 *
 * Basic structural tests for ApplySchemaCommand.
 *
 * @package App\Schema\Application\Command\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Tests;

use App\Schema\Application\Command\ApplySchemaCommand;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies ApplySchemaCommand stores its constructor arguments as public readonly properties.
 */
#[CoversClass(className: ApplySchemaCommand::class)]
final class ApplySchemaCommandBaseTest extends ApplySchemaCommandTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(ApplySchemaCommand::class, $this->class);
    }

    public function testSnapshotIsStoredCorrectly(): void
    {
        $snapshot = ['version' => 1, 'collections' => [], 'relations' => []];
        $command  = new ApplySchemaCommand(snapshot: $snapshot);

        $this->assertSame($snapshot, $command->snapshot);
    }

    public function testForceDefaultsToFalse(): void
    {
        $command = new ApplySchemaCommand(snapshot: []);

        $this->assertFalse($command->force);
    }

    public function testForceCanBeSetToTrue(): void
    {
        $command = new ApplySchemaCommand(snapshot: [], force: true);

        $this->assertTrue($command->force);
    }
}
