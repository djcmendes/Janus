<?php

/**
 * @file CollectionMetaRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all CollectionMetaRepository test cases.
 *
 * Strategy: CollectionMetaRepository is declared `final` and cannot be mocked
 * directly. It is instantiated as a real object. Its Doctrine dependencies
 * (ManagerRegistry, EntityManagerInterface, UnitOfWork, ClassMetadata) are all
 * mockable and are wired together so the real repository logic executes against
 * controlled stubs. CollectionMetaMapper is pure (no dependencies) and is used
 * as a real instance.
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use App\Collections\Infrastructure\Persistence\Doctrine\Mapper\CollectionMetaMapper;
use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * CollectionMetaRepository test suites.
 */
#[CoversClass(CollectionMetaRepository::class)]
abstract class CollectionMetaRepositoryTest extends TestCase
{
    /**
     * Mock of the Doctrine manager registry — entry point for repository resolution.
     * @var MockObject&ManagerRegistry
     */
    protected MockObject $registry;

    /**
     * Mock of the Doctrine entity manager — controls persist, flush, find, and query builder creation.
     * @var MockObject&EntityManagerInterface
     */
    protected MockObject $entityManager;

    /**
     * Mock of the Doctrine ClassMetadata for the CollectionMetaEntity persistence model.
     * @var MockObject&ClassMetadata
     */
    protected MockObject $classMetadata;

    /**
     * Mock of the Doctrine UnitOfWork — controls entity persister resolution for findOneBy/findBy.
     * @var MockObject&UnitOfWork
     */
    protected MockObject $unitOfWork;

    /**
     * Mock of the Doctrine EntityPersister — controls the results of findOneBy/findBy/count queries.
     * @var MockObject&EntityPersister
     */
    protected MockObject $persister;

    /**
     * Real CollectionMetaMapper instance — pure service with no dependencies to mock.
     * @var CollectionMetaMapper
     */
    protected CollectionMetaMapper $mapper;

    /**
     * The system under test — real CollectionMetaRepository backed by mocked Doctrine.
     * @var CollectionMetaRepository
     */
    protected CollectionMetaRepository $class;

    /**
     * Reflection of CollectionMetaRepository for reading private/inherited properties.
     * @var ReflectionClass<CollectionMetaRepository>
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
        $this->classMetadata = $this->getMockBuilder(ClassMetadata::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->classMetadata->name = CollectionMetaEntity::class;

        $this->persister = $this->createMock(EntityPersister::class);

        $this->unitOfWork = $this->getMockBuilder(UnitOfWork::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->unitOfWork->method('getEntityPersister')->willReturn($this->persister);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper = new CollectionMetaMapper();

        $this->class      = new CollectionMetaRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(CollectionMetaRepository::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->registry,
            $this->entityManager,
            $this->classMetadata,
            $this->unitOfWork,
            $this->persister,
            $this->mapper,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Creates a fully-populated domain CollectionMeta for use in save/delete tests.
     *
     * @return CollectionMeta A hydrated domain entity with deterministic test metadata.
     */
    protected function makeCollectionMeta(): CollectionMeta
    {
        $collection = new CollectionMeta('articles');
        $collection->setLabel('Articles');

        return $collection;
    }

    /**
     * Creates a fully-populated CollectionMetaEntity for use as mock find results.
     *
     * @return CollectionMetaEntity A hydrated Doctrine persistence model with deterministic test values.
     */
    protected function makeCollectionMetaEntity(): CollectionMetaEntity
    {
        return (new CollectionMetaEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setName('articles')
            ->setLabel('Articles')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
    }
}
