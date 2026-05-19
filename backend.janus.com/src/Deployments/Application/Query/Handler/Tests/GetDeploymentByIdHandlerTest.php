<?php

/**
 * @file GetDeploymentByIdHandlerTest.php
 *
 * Abstract base providing setUp / tearDown for GetDeploymentByIdHandler test cases.
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\Query\Handler\GetDeploymentByIdHandler;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for GetDeploymentByIdHandler test suites.
 */
#[CoversClass(className: GetDeploymentByIdHandler::class)]
abstract class GetDeploymentByIdHandlerTest extends TestCase
{
    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $repository;

    /** @var GetDeploymentByIdHandler */
    protected GetDeploymentByIdHandler $class;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->class      = new GetDeploymentByIdHandler(repository: $this->repository);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class);
    }
}
