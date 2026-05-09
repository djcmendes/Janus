<?php

/**
 * @file DeploymentProviderRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown and shared Doctrine mock infrastructure
 * for all DeploymentProviderRepository test cases.
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use App\Deployments\Infrastructure\Repository\DeploymentProviderRepository;
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
 * Common setup, teardown, and shared Doctrine mock infrastructure for DeploymentProviderRepository test suites.
 */
#[CoversClass(DeploymentProviderRepository::class)]
abstract class DeploymentProviderRepositoryTest extends TestCase
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

    /** @var DeploymentProviderMapper */
    protected DeploymentProviderMapper $mapper;

    /** @var DeploymentProviderRepository */
    protected DeploymentProviderRepository $class;

    /** @var ReflectionClass<DeploymentProviderRepository> */
    protected ReflectionClass $reflection;

    /**
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
        $this->classMetadata->name = DeploymentProviderEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper     = new DeploymentProviderMapper();
        $this->class      = new DeploymentProviderRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(DeploymentProviderRepository::class);
    }

    /**
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
     * Creates a domain DeploymentProvider for use in save()/delete() tests.
     *
     * @return DeploymentProvider
     */
    protected function makeProvider(): DeploymentProvider
    {
        return new DeploymentProvider(
            name: 'Netlify Production',
            type: DeploymentProviderType::NETLIFY,
            url:  'https://api.netlify.com/build_hooks/abc123',
        );
    }

    /**
     * Creates a DeploymentProviderEntity for use as a mock query/find result.
     *
     * @return DeploymentProviderEntity
     */
    protected function makeProviderEntity(): DeploymentProviderEntity
    {
        return (new DeploymentProviderEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setName('Netlify Production')
            ->setType(DeploymentProviderType::NETLIFY)
            ->setUrl('https://api.netlify.com/build_hooks/abc123')
            ->setOptions(null)
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(null);
    }
}
