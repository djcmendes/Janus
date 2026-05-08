<?php

/**
 * @file GetCollectionByNameHandlerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all GetCollectionByNameHandler test cases.
 *
 * Strategy: GetCollectionByNameHandler is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its only dependency,
 * CollectionMetaRepositoryInterface, is an interface and is mocked normally.
 *
 * @package App\Collections\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler\Tests;

use App\Collections\Application\Query\Handler\GetCollectionByNameHandler;
use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * GetCollectionByNameHandler test suites.
 */
#[CoversClass(GetCollectionByNameHandler::class)]
abstract class GetCollectionByNameHandlerTest extends TestCase
{
    /**
     * Mock of the domain repository interface.
     * @var MockObject&CollectionMetaRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test — real GetCollectionByNameHandler backed by a mocked repository.
     * @var GetCollectionByNameHandler
     */
    protected GetCollectionByNameHandler $class;

    /**
     * Reflection of GetCollectionByNameHandler class.
     * @var ReflectionClass<GetCollectionByNameHandler>
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
        $this->class      = new GetCollectionByNameHandler($this->repository);
        $this->reflection = new ReflectionClass(GetCollectionByNameHandler::class);
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

        return $collection;
    }
}
