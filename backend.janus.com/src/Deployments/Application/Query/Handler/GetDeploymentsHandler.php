<?php

/**
 * @file GetDeploymentsHandler.php
 *
 * CQRS query handler — retrieves a paginated list of deployment providers.
 *
 * @package App\Deployments\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentsQuery;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

/**
 * Returns a paginated array of DeploymentProviderDtos and the total provider count.
 */
final class GetDeploymentsHandler
{
    /**
     * @param DeploymentProviderRepositoryInterface $repository Provider persistence gateway.
     */
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /**
     * Handles the query and returns paginated DTOs with a total count.
     *
     * @param  GetDeploymentsQuery                          $query Pagination parameters.
     * @return array{data: DeploymentProviderDto[], total: int}    Result set and total count.
     */
    public function handle(GetDeploymentsQuery $query): array
    {
        return [
            'data'  => array_map(
                DeploymentProviderDto::fromEntity(...),
                $this->repository->findPaginated($query->limit, $query->offset),
            ),
            'total' => $this->repository->countAll(),
        ];
    }
}
