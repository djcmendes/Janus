<?php

/**
 * @file CreateDeploymentHandlerTest.php
 *
 * Abstract base providing setUp / tearDown for CreateDeploymentHandler test cases.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\CreateDeploymentHandler;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for CreateDeploymentHandler test suites.
 */
#[CoversClass(className: CreateDeploymentHandler::class)]
abstract class CreateDeploymentHandlerTest extends TestCase
{
    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $repository;

    /** @var CreateDeploymentHandler */
    protected CreateDeploymentHandler $class;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->class      = new CreateDeploymentHandler(repository: $this->repository);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class);
    }
}
