<?php

/**
 * @file SchemaDiffServiceBaseTest.php
 *
 * Basic structural tests for SchemaDiffService.
 *
 * @package App\Schema\Domain\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Domain\Service\Tests;

use App\Schema\Domain\Service\SchemaDiffService;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies SchemaDiffService is instantiable and its diff output has the expected top-level shape.
 */
#[CoversClass(className: SchemaDiffService::class)]
final class SchemaDiffServiceBaseTest extends SchemaDiffServiceTest
{
    public function testClassIsInstantiable(): void
    {
        $this->assertInstanceOf(SchemaDiffService::class, $this->class);
    }

    public function testDiffReturnsCollectionsKey(): void
    {
        $result = $this->class->diff($this->emptySnapshot(), $this->emptySnapshot());

        $this->assertArrayHasKey('collections', $result);
    }

    public function testDiffReturnsFieldsKey(): void
    {
        $result = $this->class->diff($this->emptySnapshot(), $this->emptySnapshot());

        $this->assertArrayHasKey('fields', $result);
    }

    public function testDiffReturnsRelationsKey(): void
    {
        $result = $this->class->diff($this->emptySnapshot(), $this->emptySnapshot());

        $this->assertArrayHasKey('relations', $result);
    }
}
