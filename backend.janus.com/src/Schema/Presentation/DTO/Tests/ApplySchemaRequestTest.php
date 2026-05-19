<?php

/**
 * @file ApplySchemaRequestTest.php
 *
 * Abstract base for ApplySchemaRequest test suites.
 *
 * @package App\Schema\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\DTO\Tests;

use App\Schema\Presentation\DTO\ApplySchemaRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Shared setup for ApplySchemaRequest tests.
 */
#[CoversClass(className: ApplySchemaRequest::class)]
abstract class ApplySchemaRequestTest extends TestCase
{
    protected ApplySchemaRequest $class;

    public function setUp(): void
    {
        $this->class = new ApplySchemaRequest();
    }

    public function tearDown(): void
    {
        unset($this->class);
    }
}
