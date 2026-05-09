<?php

/**
 * @file GetDeploymentsHandlerTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for GetDeploymentsHandler test cases.
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\Query\Handler\GetDeploymentsHandler;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for GetDeploymentsHandler test suites.
 */
#[CoversClass(GetDeploymentsHandler::class)]
abstract class GetDeploymentsHandlerTest extends TestCase
{
    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $repository;

    /** @var GetDeploymentsHandler */
    protected GetDeploymentsHandler $class;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->class      = new GetDeploymentsHandler(repository: $this->repository);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class);
    }
}
