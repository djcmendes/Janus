<?php

/**
 * @file DeploymentProviderTest.php
 *
 * Abstract base providing setUp / tearDown and shared factory helpers
 * for all DeploymentProvider domain entity test cases.
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeploymentProvider entity test suites.
 */
#[CoversClass(className: DeploymentProvider::class)]
abstract class DeploymentProviderTest extends TestCase
{
    /** @var DeploymentProvider The system under test. */
    protected DeploymentProvider $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = new DeploymentProvider(
            name: 'Netlify Production',
            type: DeploymentProviderType::NETLIFY,
            url:  'https://api.netlify.com/build_hooks/abc123',
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
     * Builds a DeploymentProvider via reconstitute() with deterministic test values.
     *
     * @param string                        $id        UUID string.
     * @param string                        $name      Display name.
     * @param DeploymentProviderType        $type      Integration type.
     * @param string                        $url       Build hook URL.
     * @param array<string, mixed>|null     $options   Extra options.
     * @param bool                          $isActive  Active flag.
     * @param \DateTimeImmutable|null       $createdAt Creation timestamp.
     * @param \DateTimeImmutable|null       $updatedAt Last-modification timestamp.
     *
     * @return DeploymentProvider
     */
    protected function makeReconstituted(
        string                 $id        = 'aaaaaaaa-0000-7000-8000-000000000001',
        string                 $name      = 'Netlify Production',
        DeploymentProviderType $type      = DeploymentProviderType::NETLIFY,
        string                 $url       = 'https://api.netlify.com/build_hooks/abc123',
        ?array                 $options   = null,
        bool                   $isActive  = true,
        ?\DateTimeImmutable    $createdAt = null,
        ?\DateTimeImmutable    $updatedAt = null,
    ): DeploymentProvider {
        return DeploymentProvider::reconstitute(
            id:        $id,
            name:      $name,
            type:      $type,
            url:       $url,
            options:   $options,
            isActive:  $isActive,
            createdAt: $createdAt ?? new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: $updatedAt,
        );
    }
}
