<?php

/**
 * @file UpdateCollectionHandlerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all UpdateCollectionHandler test cases.
 *
 * Strategy: UpdateCollectionHandler is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its only dependency,
 * CollectionMetaRepositoryInterface, is an interface and is mocked normally.
 *
 * @package App\Collections\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler\Tests;

use App\Collections\Application\Command\Handler\UpdateCollectionHandler;
use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * UpdateCollectionHandler test suites.
 */
#[CoversClass(className: UpdateCollectionHandler::class)]
abstract class UpdateCollectionHandlerTest extends TestCase
{
    /**
     * Mock of the collection repository interface.
     * @var MockObject&CollectionMetaRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var UpdateCollectionHandler
     */
    protected UpdateCollectionHandler $class;

    /**
     * Reflection of UpdateCollectionHandler class.
     * @var ReflectionClass<UpdateCollectionHandler>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(CollectionMetaRepositoryInterface::class);
        $this->class      = new UpdateCollectionHandler($this->repository);
        $this->reflection = new ReflectionClass(UpdateCollectionHandler::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated CollectionMeta entity for use in test assertions.
     *
     * @return CollectionMeta A hydrated entity with deterministic test metadata.
     */
    protected function makeCollectionMeta(): CollectionMeta
    {
        $collection = new CollectionMeta('articles');
        $collection->setLabel('Articles');
        $collection->setIcon('mdi-file');

        return $collection;
    }
}
