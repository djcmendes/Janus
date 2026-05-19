<?php

/**
 * @file ActivityRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all ActivityRepository test cases.
 *
 * Strategy: ActivityRepository is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its Doctrine dependencies (ManagerRegistry,
 * EntityManagerInterface, QueryBuilder, Query, ClassMetadata) are all mockable and
 * are wired together so the real repository logic executes against controlled stubs.
 * ActivityMapper is pure (no dependencies) and is used as a real instance.
 *
 * @package App\Activity\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Repository\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use App\Activity\Infrastructure\Repository\ActivityRepository;
use DateTimeImmutable;
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
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * ActivityRepository test suites.
 */
#[CoversClass(className: ActivityRepository::class)]
abstract class ActivityRepositoryTest extends TestCase
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
     * Mock of the Doctrine ClassMetadata for the ActivityEntity persistence model.
     * Created with disableOriginalConstructor; name is set explicitly.
     * @var MockObject&ClassMetadata<ActivityEntity>
     */
    protected MockObject $classMetadata;

    /**
     * Mock of the Doctrine QueryBuilder — all fluent methods return self.
     * @var MockObject&QueryBuilder
     */
    protected MockObject $queryBuilder;

    /**
     * Mock of the Doctrine Query returned by QueryBuilder::getQuery().
     * @var MockObject&Query<int, ActivityEntity>
     */
    protected MockObject $query;

    /**
     * Real ActivityMapper instance — pure service with no dependencies to mock.
     * @var ActivityMapper
     */
    protected ActivityMapper $mapper;

    /**
     * The system under test — real ActivityRepository backed by mocked Doctrine.
     * @var ActivityRepository
     */
    protected ActivityRepository $class;

    /**
     * Reflection of ActivityRepository for reading private/inherited properties.
     * @var ReflectionClass<ActivityRepository>
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
        $this->query        = $this->createMock(type: Query::class);
        $this->queryBuilder = $this->createMock(type: QueryBuilder::class);

        foreach ([ 'select', 'from', 'orderBy', 'setMaxResults', 'setFirstResult', 'andWhere', 'setParameter' ] as $method) {
            $this->queryBuilder->method(constraint: $method)
                               ->willReturn(value: $this->queryBuilder);
        }

        $this->queryBuilder->method(constraint: 'getQuery')
                           ->willReturn(value: $this->query);

        $this->classMetadata = $this->getMockBuilder(className: ClassMetadata::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $this->classMetadata->name = ActivityEntity::class;

        $this->entityManager = $this->createMock(type: EntityManagerInterface::class);

        $this->entityManager->method(constraint: 'createQueryBuilder')
                            ->willReturn(value: $this->queryBuilder);

        $this->entityManager->method(constraint: 'getClassMetadata')
                            ->willReturn(value: $this->classMetadata);

        $this->registry = $this->createMock(type: ManagerRegistry::class);

        $this->registry->method(constraint: 'getManagerForClass')
                       ->willReturn(value: $this->entityManager);

        $this->mapper = new ActivityMapper();

        $this->class      = new ActivityRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(objectOrClass:  ActivityRepository::class);
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
     * Creates a fully-populated domain Activity for use in record() tests.
     *
     * @param string      $action     The action type (e.g. 'create', 'update', 'delete').
     * @param string|null $collection The collection the action was performed on, or null.
     * @param string|null $item       The item identifier affected by the action, or null.
     *
     * @return Activity A hydrated domain entity with deterministic test metadata.
     */
    protected function makeActivity(
        string  $action     = 'create',
        ?string $collection = 'posts',
        ?string $item       = '1',
    ): Activity {
        $activity = new Activity(
            action:     $action,
            collection: $collection,
            item:       $item
        );
        $activity->setUserId(userId: 'bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp(ip: '127.0.0.1');
        $activity->setUserAgent(userAgent: 'PHPUnit');

        return $activity;
    }

    /**
     * Creates a fully-populated ActivityEntity for use as mock query / find results.
     *
     * @param string      $action     The action type.
     * @param string|null $collection The collection name, or null.
     * @param string|null $item       The item identifier, or null.
     *
     * @return ActivityEntity A hydrated Doctrine persistence model with deterministic test values.
     */
    protected function makeActivityEntity(
        string  $action     = 'create',
        ?string $collection = 'posts',
        ?string $item       = '1',
    ): ActivityEntity {
        return (new ActivityEntity())->setId(id: Uuid::fromString(uuid: 'aaaaaaaa-0000-7000-8000-000000000001'))
                                     ->setAction(action: $action)
                                     ->setCollection(collection: $collection)
                                     ->setItem(item: $item)
                                     ->setUserId(userId: 'bbbbbbbb-0000-7000-8000-000000000002')
                                     ->setIp(ip: '127.0.0.1')
                                     ->setUserAgent(userAgent: 'PHPUnit')
                                     ->setTimestamp(timestamp: new DateTimeImmutable(datetime: '2024-01-01T00:00:00+00:00'));
    }
}
