<?php

/**
 * @file DashboardMapperTest.php
 *
 * Abstract base for DashboardMapper test suites.
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper\DashboardMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DashboardMapper test suites.
 */
#[CoversClass(DashboardMapper::class)]
abstract class DashboardMapperTest extends TestCase
{
    /** @var DashboardMapper The system under test. */
    protected DashboardMapper $class;

    /** @var \DateTimeImmutable Deterministic creation timestamp used in test data. */
    protected \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable Deterministic update timestamp used in test data. */
    protected \DateTimeImmutable $updatedAt;

    /**
     * Builds the SUT and shared timestamps before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class     = new DashboardMapper();
        $this->createdAt = new \DateTimeImmutable('2024-01-01T00:00:00Z');
        $this->updatedAt = new \DateTimeImmutable('2024-06-01T00:00:00Z');
    }

    /**
     * Releases references after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->class, $this->createdAt, $this->updatedAt);
    }

    /**
     * Builds a fully-hydrated DashboardEntity with deterministic test values.
     *
     * @return DashboardEntity
     */
    protected function makeEntity(): DashboardEntity
    {
        return (new DashboardEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setName('Test Dashboard')
            ->setIcon('chart')
            ->setNote('A note')
            ->setUserId('user-uuid-001')
            ->setCreatedAt($this->createdAt)
            ->setUpdatedAt($this->updatedAt);
    }

    /**
     * Builds a domain Dashboard via reconstitute() with deterministic test values.
     *
     * @return Dashboard
     */
    protected function makeDomain(): Dashboard
    {
        return Dashboard::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Test Dashboard',
            icon:      'chart',
            note:      'A note',
            userId:    'user-uuid-001',
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
        );
    }
}
