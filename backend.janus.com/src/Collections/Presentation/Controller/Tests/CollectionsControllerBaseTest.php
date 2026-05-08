<?php

/**
 * @file CollectionsControllerBaseTest.php
 *
 * Constructor injection verification tests for CollectionsController.
 *
 * @package App\Collections\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Presentation\Controller\Tests;

use App\Collections\Application\Command\Handler\CreateCollectionHandler;
use App\Collections\Application\Command\Handler\DeleteCollectionHandler;
use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Application\Query\Handler\GetCollectionByNameHandler;
use App\Collections\Application\Query\Handler\GetCollectionsHandler;
use App\Collections\Presentation\Controller\CollectionsController;
use App\Heimdall\Domain\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Verifies that CollectionsController stores each injected dependency
 * in the correct private property after construction.
 */
#[CoversClass(CollectionsController::class)]
final class CollectionsControllerBaseTest extends CollectionsControllerTest
{
    /**
     * Test that the guard property holds the injected RequestGuard instance.
     *
     * @throws ReflectionException
     */
    public function testGuardIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'guard'))->getValue($this->class);

        $this->assertInstanceOf(RequestGuard::class, $value);
        $this->assertSame($this->guard, $value);
    }

    /**
     * Test that the getCollectionsHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testGetCollectionsHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'getCollectionsHandler'))->getValue($this->class);

        $this->assertInstanceOf(GetCollectionsHandler::class, $value);
        $this->assertSame($this->getCollectionsHandler, $value);
    }

    /**
     * Test that the getCollectionByNameHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testGetCollectionByNameHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'getCollectionByNameHandler'))->getValue($this->class);

        $this->assertInstanceOf(GetCollectionByNameHandler::class, $value);
        $this->assertSame($this->getCollectionByNameHandler, $value);
    }

    /**
     * Test that the createCollectionHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testCreateCollectionHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'createCollectionHandler'))->getValue($this->class);

        $this->assertInstanceOf(CreateCollectionHandler::class, $value);
        $this->assertSame($this->createCollectionHandler, $value);
    }

    /**
     * Test that the updateCollectionHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testUpdateCollectionHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'updateCollectionHandler'))->getValue($this->class);

        $this->assertInstanceOf(UpdateCollectionHandler::class, $value);
        $this->assertSame($this->updateCollectionHandler, $value);
    }

    /**
     * Test that the deleteCollectionHandler property holds the injected handler instance.
     *
     * @throws ReflectionException
     */
    public function testDeleteCollectionHandlerIsSetCorrectly(): void
    {
        $value = (new ReflectionProperty($this->class, 'deleteCollectionHandler'))->getValue($this->class);

        $this->assertInstanceOf(DeleteCollectionHandler::class, $value);
        $this->assertSame($this->deleteCollectionHandler, $value);
    }
}
