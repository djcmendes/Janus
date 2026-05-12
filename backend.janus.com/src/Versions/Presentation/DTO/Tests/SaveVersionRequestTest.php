<?php

/**
 * @file SaveVersionRequestTest.php
 *
 * Abstract base providing setUp / tearDown and shared instance for
 * all SaveVersionRequest test cases.
 *
 * @package App\Versions\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO\Tests;

use App\Versions\Presentation\DTO\SaveVersionRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup and shared instance for all SaveVersionRequest test suites.
 */
#[CoversClass(SaveVersionRequest::class)]
abstract class SaveVersionRequestTest extends TestCase
{
    /** @var SaveVersionRequest */
    protected SaveVersionRequest $class;

    public function setUp(): void
    {
        $this->class = new SaveVersionRequest();
    }

    public function tearDown(): void
    {
        unset($this->class);
    }
}
