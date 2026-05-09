<?php

/**
 * @file GetDeploymentsQuery.php
 *
 * CQRS query payload for retrieving a paginated list of deployment providers.
 *
 * @package App\Deployments\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query;

/**
 * Carries pagination parameters for listing all DeploymentProviders.
 */
final class GetDeploymentsQuery
{
    /**
     * @param int $limit  Maximum number of providers to return.
     * @param int $offset Zero-based record offset for pagination.
     */
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
