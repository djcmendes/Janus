<?php

/**
 * @file DashboardTest.php
 *
 * Abstract base providing setUp / tearDown and shared factory helpers
 * for all Dashboard domain entity test cases.
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for Dashboard entity test suites.
 */
#[CoversClass(className: Dashboard::class)]
abstract class DashboardTest extends TestCase
{
    /** @var Dashboard The system under test. */
    protected Dashboard $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new Dashboard(name: 'My Dashboard', icon: 'dashboard', note: 'A note', userId: 'user-uuid-001');
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
     * Builds a Dashboard via reconstitute() with deterministic test values.
     *
     * @param string             $id        UUID string.
     * @param string             $name      Display name.
     * @param string|null        $icon      Icon identifier.
     * @param string|null        $note      Note text.
     * @param string|null        $userId    Owner UUID.
     * @param \DateTimeImmutable $createdAt Creation timestamp.
     * @param \DateTimeImmutable $updatedAt Last-modification timestamp.
     *
     * @return Dashboard
     */
    protected function makeReconstituted(
        string             $id        = 'aaaaaaaa-0000-7000-8000-000000000001',
        string             $name      = 'Reconstituted',
        ?string            $icon      = null,
        ?string            $note      = null,
        ?string            $userId    = 'user-uuid-001',
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): Dashboard {
        return Dashboard::reconstitute(
            id:        $id,
            name:      $name,
            icon:      $icon,
            note:      $note,
            userId:    $userId,
            createdAt: $createdAt ?? new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: $updatedAt ?? new \DateTimeImmutable('2024-06-01T00:00:00Z'),
        );
    }
}
