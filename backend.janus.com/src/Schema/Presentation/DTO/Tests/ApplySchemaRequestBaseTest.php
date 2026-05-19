<?php

/**
 * @file ApplySchemaRequestBaseTest.php
 *
 * Basic structural tests for ApplySchemaRequest.
 *
 * @package App\Schema\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\DTO\Tests;

use App\Schema\Presentation\DTO\ApplySchemaRequest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies ApplySchemaRequest has the correct default property values.
 */
#[CoversClass(className: ApplySchemaRequest::class)]
final class ApplySchemaRequestBaseTest extends ApplySchemaRequestTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(ApplySchemaRequest::class, $this->class);
    }

    public function testSnapshotDefaultsToNull(): void
    {
        $this->assertNull($this->class->snapshot);
    }

    public function testForceDefaultsToFalse(): void
    {
        $this->assertFalse($this->class->force);
    }

    public function testSnapshotCanBeAssigned(): void
    {
        $this->class->snapshot = ['version' => 1, 'collections' => [], 'relations' => []];

        $this->assertIsArray($this->class->snapshot);
    }

    public function testForceCanBeSetToTrue(): void
    {
        $this->class->force = true;

        $this->assertTrue($this->class->force);
    }
}
