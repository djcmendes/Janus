<?php

/**
 * @file DeploymentProviderDtoTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for DeploymentProviderDto test cases.
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeploymentProviderDto test suites.
 */
#[CoversClass(className: DeploymentProviderDto::class)]
abstract class DeploymentProviderDtoTest extends TestCase
{
    /** @var DeploymentProviderDto The system under test. */
    protected DeploymentProviderDto $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentProviderDto(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Netlify Production',
            type:      'netlify',
            url:       'https://api.netlify.com/build_hooks/abc123',
            options:   ['headers' => ['X-Secret' => 'abc']],
            isActive:  true,
            createdAt: '2024-01-01T00:00:00+00:00',
            updatedAt: '2024-06-01T00:00:00+00:00',
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
     * Creates a domain DeploymentProvider for fromEntity() tests.
     *
     * @return DeploymentProvider
     */
    protected function makeProvider(): DeploymentProvider
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
