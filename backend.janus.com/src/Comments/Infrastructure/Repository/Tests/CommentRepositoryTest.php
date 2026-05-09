<?php

/**
 * @file CommentRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all CommentRepository test cases.
 *
 * Strategy: CommentRepository is declared `final` and cannot be mocked directly.
 * It is instantiated as a real object. Its Doctrine dependencies (ManagerRegistry,
 * EntityManagerInterface, QueryBuilder, Query, ClassMetadata) are all mockable and
 * are wired together so the real repository logic executes against controlled stubs.
 * CommentMapper is pure (no dependencies) and is used as a real instance.
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use App\Comments\Infrastructure\Repository\CommentRepository;
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
 * CommentRepository test suites.
 */
#[CoversClass(CommentRepository::class)]
abstract class CommentRepositoryTest extends TestCase
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
     * Mock of the Doctrine ClassMetadata for the CommentEntity persistence model.
     * Created with disableOriginalConstructor; name is set explicitly.
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
     * Real CommentMapper instance — pure service with no dependencies to mock.
     * @var CommentMapper
     */
    protected CommentMapper $mapper;

    /**
     * The system under test — real CommentRepository backed by mocked Doctrine.
     * @var CommentRepository
     */
    protected CommentRepository $class;

    /**
     * Reflection of CommentRepository for reading private/inherited properties.
     * @var ReflectionClass<CommentRepository>
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

        $this->classMetadata->name = CommentEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper = new CommentMapper();

        $this->class      = new CommentRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(CommentRepository::class);
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
     * Creates a fully-populated domain Comment for use in save() and delete() tests.
     *
     * @param string $collection The collection name.
     * @param string $item       The item identifier.
     * @param string $comment    The comment text.
     *
     * @return Comment A hydrated domain entity with deterministic test values.
     */
    protected function makeComment(
        string $collection = 'posts',
        string $item       = '1',
        string $comment    = 'Hello world',
    ): Comment {
        return new Comment($collection, $item, $comment, 'aaaaaaaa-0000-7000-8000-000000000001');
    }

    /**
     * Creates a fully-populated CommentEntity for use as mock query / find results.
     *
     * @param string $collection The collection name.
     * @param string $item       The item identifier.
     *
     * @return CommentEntity A hydrated Doctrine persistence model with deterministic test values.
     */
    protected function makeCommentEntity(
        string $collection = 'posts',
        string $item       = '1',
    ): CommentEntity {
        return (new CommentEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setCollection($collection)
            ->setItem($item)
            ->setComment('Hello world')
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }
}
