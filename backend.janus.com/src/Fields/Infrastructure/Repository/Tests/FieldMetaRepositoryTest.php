<?php

declare(strict_types=1);

namespace App\Fields\Infrastructure\Repository\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Infrastructure\Persistence\Doctrine\Entity\FieldMetaEntity;
use App\Fields\Infrastructure\Persistence\Doctrine\Mapper\FieldMetaMapper;
use App\Fields\Infrastructure\Repository\FieldMetaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[PHPUnit\Framework\Attributes\CoversClass(className: FieldMetaRepository::class)]
abstract class FieldMetaRepositoryTest extends TestCase
{
    /** @var MockObject&ManagerRegistry */
    protected MockObject $registry;

    /** @var MockObject&EntityManagerInterface */
    protected MockObject $entityManager;

    /** @var MockObject&ClassMetadata */
    protected MockObject $classMetadata;

    /** @var MockObject&QueryBuilder */
    protected MockObject $queryBuilder;

    /** @var MockObject&Query */
    protected MockObject $query;

    protected FieldMetaMapper $mapper;
    protected FieldMetaRepository $class;

    /** @var ReflectionClass<FieldMetaRepository> */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $this->query        = $this->createMock(Query::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);

        $this->queryBuilder->method('select')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('from')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('delete')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('where')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('orderBy')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setMaxResults')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setFirstResult')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('andWhere')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('setParameter')->willReturn($this->queryBuilder);
        $this->queryBuilder->method('getQuery')->willReturn($this->query);

        $this->classMetadata       = $this->getMockBuilder(ClassMetadata::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();
        $this->classMetadata->name = FieldMetaEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper     = new FieldMetaMapper();
        $this->class      = new FieldMetaRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(FieldMetaRepository::class);
    }

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

    protected function makeFieldMeta(): FieldMeta
    {
        return new FieldMeta('articles', 'title', FieldType::STRING);
    }

    protected function makeFieldMetaEntity(): FieldMetaEntity
    {
        return (new FieldMetaEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setCollection('articles')
            ->setField('title')
            ->setType(FieldType::STRING)
            ->setLabel(null)
            ->setNote(null)
            ->setRequired(false)
            ->setReadonly(false)
            ->setHidden(false)
            ->setSortOrder(0)
            ->setInterface(null)
            ->setOptions(null)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(null);
    }
}
