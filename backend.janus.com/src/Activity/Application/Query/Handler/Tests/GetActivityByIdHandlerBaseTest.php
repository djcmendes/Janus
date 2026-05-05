<?php

/**
 * @file GetActivityByIdHandlerBaseTest.php
 *
 * Tests for GetActivityByIdHandler construction and dependency wiring.
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\Query\Handler\GetActivityByIdHandler;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that GetActivityByIdHandler stores its repository dependency correctly after construction.
 */
#[CoversClass(GetActivityByIdHandler::class)]
final class GetActivityByIdHandlerBaseTest extends GetActivityByIdHandlerTest
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
     * Test that GetActivityByIdHandler can be instantiated with a mocked repository.
     */
    public function testImplementsNoInterfaceButIsInstantiable(): void
    {
        $this->assertInstanceOf(GetActivityByIdHandler::class, $this->class);
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
