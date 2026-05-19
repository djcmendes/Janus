<?php

/**
 * @file DeploymentMapperTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentMapper test cases.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and factory helpers for DeploymentMapper test suites.
 */
#[CoversClass(className: DeploymentMapper::class)]
abstract class DeploymentMapperTest extends TestCase
{
    /** @var DeploymentMapper The system under test. */
    protected DeploymentMapper $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentMapper();
    }

    /**
     * Releases the SUT reference after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->class);
    }

    /**
     * Creates a fully-hydrated DeploymentEntity with deterministic test values.
     *
     * @return DeploymentEntity
     */
    protected function makeEntity(): DeploymentEntity
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

    /**
     * Creates a domain Deployment via reconstitute() with deterministic test values.
     *
     * @return Deployment
     */
    protected function makeDomain(): Deployment
    {
        return Deployment::reconstitute(
            id:          'aaaaaaaa-0000-7000-8000-000000000001',
            providerId:  'pppppppp-0000-7000-8000-000000000001',
            status:      DeploymentRunStatus::SUCCESS,
            log:         '[HTTP 200] ok',
            triggeredBy: 'uuuuuuuu-0000-7000-8000-000000000001',
            startedAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            completedAt: new \DateTimeImmutable('2024-01-01T00:01:00Z'),
        );
    }
}
