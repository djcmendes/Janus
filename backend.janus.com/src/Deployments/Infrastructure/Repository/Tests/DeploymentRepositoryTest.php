<?php

/**
 * @file DeploymentRepositoryTest.php
 *
 * Abstract base providing setUp / tearDown and shared Doctrine mock infrastructure
 * for all DeploymentRepository test cases.
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use App\Deployments\Infrastructure\Repository\DeploymentRepository;
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
 * Common setup, teardown, and shared Doctrine mock infrastructure for DeploymentRepository test suites.
 */
#[CoversClass(className: DeploymentRepository::class)]
abstract class DeploymentRepositoryTest extends TestCase
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

    /** @var DeploymentMapper */
    protected DeploymentMapper $mapper;

    /** @var DeploymentRepository */
    protected DeploymentRepository $class;

    /** @var ReflectionClass<DeploymentRepository> */
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
        $this->classMetadata->name = DeploymentEntity::class;

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('createQueryBuilder')->willReturn($this->queryBuilder);
        $this->entityManager->method('getClassMetadata')->willReturn($this->classMetadata);

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')->willReturn($this->entityManager);

        $this->mapper     = new DeploymentMapper();
        $this->class      = new DeploymentRepository(registry: $this->registry, mapper: $this->mapper);
        $this->reflection = new ReflectionClass(DeploymentRepository::class);
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
     * Creates a domain Deployment for use in save() tests.
     *
     * @return Deployment
     */
    protected function makeDeployment(): Deployment
    {
        return new Deployment(
            providerId:  'pppppppp-0000-7000-8000-000000000001',
            triggeredBy: 'uuuuuuuu-0000-7000-8000-000000000001',
        );
    }

    /**
     * Creates a DeploymentEntity for use as a mock query/find result.
     *
     * @return DeploymentEntity
     */
    protected function makeDeploymentEntity(): DeploymentEntity
    {
        return (new DeploymentEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setProviderId('pppppppp-0000-7000-8000-000000000001')
            ->setStatus(DeploymentRunStatus::SUCCESS)
            ->setLog('[HTTP 200] ok')
            ->setTriggeredBy('uuuuuuuu-0000-7000-8000-000000000001')
            ->setStartedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setCompletedAt(new \DateTimeImmutable('2024-01-01T00:01:00Z'));
    }
}
