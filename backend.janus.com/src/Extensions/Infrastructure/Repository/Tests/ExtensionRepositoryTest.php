<?php

declare(strict_types=1);

namespace App\Extensions\Infrastructure\Repository\Tests;

use App\Extensions\Domain\Entity\Extension;
use App\Extensions\Domain\Enum\ExtensionType;
use App\Extensions\Infrastructure\Persistence\Doctrine\Entity\ExtensionEntity;
use App\Extensions\Infrastructure\Persistence\Doctrine\Mapper\ExtensionMapper;
use App\Extensions\Infrastructure\Repository\ExtensionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[CoversClass(ExtensionRepository::class)]
abstract class ExtensionRepositoryTest extends TestCase
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

    protected ExtensionMapper $mapper;
    protected ExtensionRepository $class;

    /** @var ReflectionClass<ExtensionRepository> */
    protected ReflectionClass $reflection;

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

        $this->classMetadata       = $this->getMockBuilder(ClassMetadata::class)
                                          ->disableOriginalConstructor()
                                          ->getMock();
        $this->classMetadata->name = ExtensionEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper     = new ExtensionMapper();
        $this->class      = new ExtensionRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(ExtensionRepository::class);
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

    protected function makeExtension(): Extension
    {
        return new Extension(
            name:    'my-hook',
            type:    ExtensionType::HOOK,
            version: '1.0.0',
        );
    }

    protected function makeExtensionEntity(): ExtensionEntity
    {
        return (new ExtensionEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setName('my-hook')
            ->setType(ExtensionType::HOOK)
            ->setVersion('1.0.0')
            ->setEnabled(false)
            ->setDescription(null)
            ->setMeta(null)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'));
    }
}
