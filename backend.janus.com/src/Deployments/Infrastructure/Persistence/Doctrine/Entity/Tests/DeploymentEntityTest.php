<?php

/**
 * @file DeploymentEntityTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentEntity test cases.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and factory helpers for DeploymentEntity test suites.
 */
#[CoversClass(DeploymentEntity::class)]
abstract class DeploymentEntityTest extends TestCase
{
    /** @var DeploymentEntity The system under test. */
    protected DeploymentEntity $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = $this->makeEntity();
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
}
