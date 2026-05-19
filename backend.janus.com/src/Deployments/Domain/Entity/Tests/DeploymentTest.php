<?php

/**
 * @file DeploymentTest.php
 *
 * Abstract base providing setUp / tearDown and shared factory helpers
 * for all Deployment domain entity test cases.
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for Deployment entity test suites.
 */
#[CoversClass(className: Deployment::class)]
abstract class DeploymentTest extends TestCase
{
    /** @var Deployment The system under test. */
    protected Deployment $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new Deployment(
            providerId:  'pppppppp-0000-7000-8000-000000000001',
            triggeredBy: 'uuuuuuuu-0000-7000-8000-000000000001',
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
     * Builds a Deployment via reconstitute() with deterministic test values.
     *
     * @param string                   $id          UUID string.
     * @param string                   $providerId  Provider UUID.
     * @param DeploymentRunStatus      $status      Lifecycle status.
     * @param string|null              $log         Log text.
     * @param string|null              $triggeredBy Triggering user UUID.
     * @param \DateTimeImmutable|null  $startedAt   Start timestamp.
     * @param \DateTimeImmutable|null  $completedAt Completion timestamp.
     *
     * @return Deployment
     */
    protected function makeReconstituted(
        string              $id          = 'aaaaaaaa-0000-7000-8000-000000000001',
        string              $providerId  = 'pppppppp-0000-7000-8000-000000000001',
        DeploymentRunStatus $status      = DeploymentRunStatus::SUCCESS,
        ?string             $log         = '[HTTP 200] ok',
        ?string             $triggeredBy = 'uuuuuuuu-0000-7000-8000-000000000001',
        ?\DateTimeImmutable $startedAt   = null,
        ?\DateTimeImmutable $completedAt = null,
    ): Deployment {
        return Deployment::reconstitute(
            id:          $id,
            providerId:  $providerId,
            status:      $status,
            log:         $log,
            triggeredBy: $triggeredBy,
            startedAt:   $startedAt ?? new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            completedAt: $completedAt,
        );
    }
}
