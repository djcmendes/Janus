<?php

/**
 * @file DeploymentProviderMapperTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentProviderMapper test cases.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

/**
 * Common setup, teardown, and factory helpers for DeploymentProviderMapper test suites.
 */
#[CoversClass(className: DeploymentProviderMapper::class)]
abstract class DeploymentProviderMapperTest extends TestCase
{
    /** @var DeploymentProviderMapper The system under test. */
    protected DeploymentProviderMapper $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentProviderMapper();
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

    /**
     * Creates a domain DeploymentProvider via reconstitute() with deterministic test values.
     *
     * @return DeploymentProvider
     */
    protected function makeDomain(): DeploymentProvider
    {
        return DeploymentProvider::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Netlify Production',
            type:      DeploymentProviderType::NETLIFY,
            url:       'https://api.netlify.com/build_hooks/abc123',
            options:   ['headers' => ['X-Secret' => 'abc']],
            isActive:  true,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new \DateTimeImmutable('2024-06-01T00:00:00Z'),
        );
    }
}
