<?php

/**
 * @file UpdateVersionRequestTest.php
 *
 * Abstract base providing setUp / tearDown and shared instance for
 * all UpdateVersionRequest test cases.
 *
 * @package App\Versions\Presentation\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Presentation\DTO\Tests;

use App\Versions\Presentation\DTO\UpdateVersionRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup and shared instance for all UpdateVersionRequest test suites.
 */
#[CoversClass(UpdateVersionRequest::class)]
abstract class UpdateVersionRequestTest extends TestCase
{
    /** @var UpdateVersionRequest */
    protected UpdateVersionRequest $class;

    public function setUp(): void
    {
        $this->class = new UpdateVersionRequest();
    }

    public function tearDown(): void
    {
        unset($this->class);
    }
}
