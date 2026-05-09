<?php

/**
 * @file DeploymentProviderEntityTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentProviderEntity test cases.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and factory helpers for DeploymentProviderEntity test suites.
 */
#[CoversClass(DeploymentProviderEntity::class)]
abstract class DeploymentProviderEntityTest extends TestCase
{
    /** @var DeploymentProviderEntity The system under test. */
    protected DeploymentProviderEntity $class;

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
     * Creates a fully-hydrated DeploymentProviderEntity with deterministic test values.
     *
     * @return DeploymentProviderEntity
     */
    protected function makeEntity(): DeploymentProviderEntity
    {
        return (new DeploymentProviderEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setName('Netlify Production')
            ->setType(DeploymentProviderType::NETLIFY)
            ->setUrl('https://api.netlify.com/build_hooks/abc123')
            ->setOptions(['headers' => ['X-Secret' => 'abc']])
            ->setIsActive(true)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(new \DateTimeImmutable('2024-06-01T00:00:00Z'));
    }
}
