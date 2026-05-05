<?php

/**
 * @file GetActivityHandlerBaseTest.php
 *
 * Tests for GetActivityHandler construction and dependency wiring.
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetActivityHandler stores its repository dependency correctly after construction.
 */
#[CoversClass(GetActivityHandler::class)]
final class GetActivityHandlerBaseTest extends GetActivityHandlerTest
{
    /**
     * Test that the handler stores the injected repository in its repository property.
     */
    public function testConstructorStoresRepository(): void
    {
        $property = $this->reflection->getProperty('repository');
        $property->setAccessible(true);

        $this->assertSame($this->repository, $property->getValue($this->class));
    }

    /**
     * Test that GetActivityHandler can be instantiated with a mocked repository.
     */
    public function testIsInstantiable(): void
    {
        $this->assertInstanceOf(GetActivityHandler::class, $this->class);
    }

    /**
     * Test that the stored repository implements ActivityRepositoryInterface.
     */
    public function testRepositoryIsActivityRepositoryInterface(): void
    {
        $property = $this->reflection->getProperty('repository');
        $property->setAccessible(true);

        $this->assertInstanceOf(ActivityRepositoryInterface::class, $property->getValue($this->class));
    }
}
