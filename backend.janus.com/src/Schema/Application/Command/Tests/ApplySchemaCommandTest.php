<?php

/**
 * @file ApplySchemaCommandTest.php
 *
 * Abstract base for ApplySchemaCommand test suites.
 *
 * @package App\Schema\Application\Command\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Application\Command\Tests;

use App\Schema\Application\Command\ApplySchemaCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup for ApplySchemaCommand tests.
 */
#[CoversClass(className: ApplySchemaCommand::class)]
abstract class ApplySchemaCommandTest extends TestCase
{
    protected ApplySchemaCommand $class;

    public function setUp(): void
    {
        $this->class = new ApplySchemaCommand(snapshot: ['version' => 1, 'collections' => [], 'relations' => []]);
    }

    public function tearDown(): void
    {
        unset($this->class);
    }
}
