<?php

/**
 * @file DeleteDeploymentHandlerTest.php
 *
 * Abstract base providing setUp / tearDown for DeleteDeploymentHandler test cases.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\DeleteDeploymentHandler;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeleteDeploymentHandler test suites.
 */
#[CoversClass(DeleteDeploymentHandler::class)]
abstract class DeleteDeploymentHandlerTest extends TestCase
{
    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $repository;

    /** @var DeleteDeploymentHandler */
    protected DeleteDeploymentHandler $class;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->class      = new DeleteDeploymentHandler(repository: $this->repository);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class);
    }
}
