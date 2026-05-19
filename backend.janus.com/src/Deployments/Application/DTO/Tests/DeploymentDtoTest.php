<?php

/**
 * @file DeploymentDtoTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentDto test cases.
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentDto;
use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeploymentDto test suites.
 */
#[CoversClass(className: DeploymentDto::class)]
abstract class DeploymentDtoTest extends TestCase
{
    /** @var DeploymentDto The system under test. */
    protected DeploymentDto $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentDto(
            id:          'aaaaaaaa-0000-7000-8000-000000000001',
            providerId:  'pppppppp-0000-7000-8000-000000000001',
            status:      'success',
            log:         '[HTTP 200] ok',
            triggeredBy: 'uuuuuuuu-0000-7000-8000-000000000001',
            startedAt:   '2024-01-01T00:00:00+00:00',
            completedAt: '2024-01-01T00:01:00+00:00',
        );
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
     * Creates a domain Deployment for fromEntity() tests.
     *
     * @return Deployment
     */
    protected function makeDeployment(): Deployment
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
