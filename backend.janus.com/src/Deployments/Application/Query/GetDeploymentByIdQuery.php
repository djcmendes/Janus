<?php

/**
 * @file GetDeploymentByIdQuery.php
 *
 * CQRS query payload for retrieving a single deployment provider by UUID.
 *
 * @package App\Deployments\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query;

/**
 * Carries the UUID of the DeploymentProvider to retrieve.
 */
final class GetDeploymentByIdQuery
{
    /**
     * @param string $id UUID of the DeploymentProvider to fetch.
     */
    public function __construct(public readonly string $id) {}
}
