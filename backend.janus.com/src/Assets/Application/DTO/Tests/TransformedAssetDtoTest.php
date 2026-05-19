<?php

/**
 * @file TransformedAssetDtoTest.php
 *
 * Abstract base for all TransformedAssetDto test suites.
 *
 * Strategy: TransformedAssetDto is a final class with no injectable
 * dependencies. Tests instantiate it directly with deterministic values —
 * no mocks required.
 *
 * @package App\Assets\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\DTO\Tests;

use App\Assets\Application\DTO\TransformedAssetDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for TransformedAssetDto tests.
 */
#[CoversClass(className: TransformedAssetDto::class)]
abstract class TransformedAssetDtoTest extends TestCase
{
    /** @var TransformedAssetDto The DTO instance under test. */
    protected TransformedAssetDto $class;

    /** @var ReflectionClass<TransformedAssetDto> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new TransformedAssetDto('binary-content', 'image/jpeg', 'photo.jpg');
        $this->reflection = new ReflectionClass(TransformedAssetDto::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }
}
