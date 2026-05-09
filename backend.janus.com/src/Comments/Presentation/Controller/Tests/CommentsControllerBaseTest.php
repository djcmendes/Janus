<?php

/**
 * @file CommentsControllerBaseTest.php
 *
 * Tests for CommentsController constructor and property initialisation.
 *
 * @package App\Comments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Presentation\Controller\Tests;

use App\Comments\Presentation\Controller\CommentsController;
use App\Heimdall\Domain\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionProperty;

/**
 * Verifies that the CommentsController stores each injected dependency
 * in the correct private property after construction.
 */
#[CoversClass(CommentsController::class)]
final class CommentsControllerBaseTest extends CommentsControllerTest
{
    /**
     * Test that the guard property holds the injected RequestGuard instance.
     */
    public function testGuardIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'guard'))->getValue($this->class);

        $this->assertInstanceOf(RequestGuard::class, $value);
        $this->assertSame($this->guard, $value);
    }
}
