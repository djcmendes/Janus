<?php

/**
 * @file VersionRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all VersionRepository test cases.
 *
 * Strategy: VersionRepository is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its Doctrine dependencies (ManagerRegistry,
 * EntityManagerInterface, QueryBuilder, Query, ClassMetadata) are all mockable and
 * are wired together so the real repository logic executes against controlled stubs.
 * VersionMapper is pure (no dependencies) and is used as a real instance.
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use App\Versions\Infrastructure\Persistence\Doctrine\Mapper\VersionMapper;
use App\Versions\Infrastructure\Repository\VersionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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
 * VersionRepository test suites.
 */
#[CoversClass(className:  VersionRepository::class)]
abstract class VersionRepositoryTest extends TestCase
{
    /**
     * Mock of the Doctrine manager registry.
     * @var MockObject&ManagerRegistry
     */
    protected MockObject $registry;

    /**
     * Mock of the Doctrine entity manager.
     * @var MockObject&EntityManagerInterface
     */
    protected MockObject $entityManager;

    /**
     * Mock of the Doctrine ClassMetadata for VersionEntity.
     * @var MockObject&ClassMetadata
     */
    protected MockObject $classMetadata;

    /**
     * Mock of the Doctrine UnitOfWork — controls entity persister resolution for findOneBy/findBy.
     * @var MockObject&UnitOfWork
     */
    protected MockObject $unitOfWork;

    /**
     * Mock of the Doctrine EntityPersister — controls results of findOneBy/findBy queries.
     * @var MockObject&EntityPersister
     */
    protected MockObject $persister;

    /**
     * Mock of the Doctrine QueryBuilder — all fluent methods return self.
     * @var MockObject&QueryBuilder
     */
    protected MockObject $queryBuilder;

    /**
     * Mock of the Doctrine Query returned by QueryBuilder::getQuery().
     * @var MockObject&Query
     */
    protected MockObject $query;

    /**
     * Real VersionMapper instance — pure service with no dependencies to mock.
     * @var VersionMapper
     */
    protected VersionMapper $mapper;

    /**
     * The system under test — real VersionRepository backed by mocked Doctrine.
     * @var VersionRepository
     */
    protected VersionRepository $class;

    /**
     * @var ReflectionClass<VersionRepository>
     */
    protected ReflectionClass $reflection;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->query        = $this->createMock(type: Query::class);
        $this->queryBuilder = $this->createMock(type: QueryBuilder::class);

        $this->queryBuilder->method('select')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('from')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('orderBy')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setMaxResults')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setFirstResult')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('andWhere')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setParameter')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('getQuery')->willReturn($this->query);

        $this->classMetadata = $this->getMockBuilder(ClassMetadata::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->classMetadata->name = VersionEntity::class;

        $this->persister = $this->createMock(EntityPersister::class);

        $this->unitOfWork = $this->getMockBuilder(UnitOfWork::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->unitOfWork->method('getEntityPersister')->willReturn($this->persister);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper = new VersionMapper();
        $this->class  = new VersionRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(VersionRepository::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->registry,
            $this->entityManager,
            $this->classMetadata,
            $this->unitOfWork,
            $this->persister,
            $this->queryBuilder,
            $this->query,
            $this->mapper,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Creates a fully-populated domain Version for use in save/delete tests.
     *
     * @return Version A hydrated domain entity with deterministic test values.
     */
    protected function makeVersion(): Version
    {
        return new Version(
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       ['title' => 'Hello'],
            delta:      null,
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
        );
    }

    /**
     * Creates a fully-populated VersionEntity for use as mock query / find results.
     *
     * @return VersionEntity A hydrated Doctrine persistence model with deterministic values.
     */
    protected function makeVersionEntity(): VersionEntity
    {
        return (new VersionEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setCollection('articles')
            ->setItem('item-uuid-1')
            ->setKey('main')
            ->setData(['title' => 'Hello'])
            ->setDelta(null)
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }
}
