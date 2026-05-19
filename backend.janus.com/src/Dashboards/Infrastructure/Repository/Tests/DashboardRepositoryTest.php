<?php

/**
 * @file DashboardRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown, shared mock infrastructure, and
 * factory helpers for all DashboardRepository test cases.
 *
 * Strategy: DashboardRepository is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its Doctrine dependencies (ManagerRegistry,
 * EntityManagerInterface, QueryBuilder, Query, ClassMetadata) are all mockable and
 * are wired together so the real repository logic executes against controlled stubs.
 * DashboardMapper is pure (no dependencies) and is used as a real instance.
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * DashboardRepository test suites.
 */
#[CoversClass(className: DashboardRepository::class)]
abstract class DashboardRepositoryTest extends TestCase
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
     * Mock of the Doctrine ClassMetadata for the DashboardEntity persistence model.
     * @var MockObject&ClassMetadata
     */
    protected MockObject $classMetadata;

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
     * Real DashboardMapper instance — pure service with no dependencies to mock.
     * @var DashboardMapper
     */
    protected DashboardMapper $mapper;

    /**
     * The system under test — real DashboardRepository backed by mocked Doctrine.
     * @var DashboardRepository
     */
    protected DashboardRepository $class;

    /**
     * Reflection of DashboardRepository for reading private/inherited properties.
     * @var ReflectionClass<DashboardRepository>
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
        $this->query        = $this->createMock(Query::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);

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

        $this->classMetadata->name = DashboardEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper = new DashboardMapper();

        $this->class      = new DashboardRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(DashboardRepository::class);
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
            $this->queryBuilder,
            $this->query,
            $this->mapper,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Creates a domain Dashboard for use in save() and delete() tests.
     *
     * @param string      $name   Dashboard name.
     * @param string|null $userId Owner UUID.
     *
     * @return Dashboard A hydrated domain entity with deterministic test values.
     */
    protected function makeDashboard(
        string  $name   = 'Test Dashboard',
        ?string $userId = 'user-uuid-001',
    ): Dashboard {
        return new Dashboard($name, null, null, $userId);
    }

    /**
     * Creates a DashboardEntity for use as a mock query/find result.
     *
     * @return DashboardEntity A hydrated Doctrine persistence model.
     */
    protected function makeDashboardEntity(): DashboardEntity
    {
        return (new DashboardEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setName('Test Dashboard')
            ->setIcon(null)
            ->setNote(null)
            ->setUserId('user-uuid-001')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(new \DateTimeImmutable('2024-06-01T00:00:00Z'));
    }
}
