<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Repository;

use App\Deployments\Domain\Entity\Deployment;

interface DeploymentRepositoryInterface
{
    public function save(Deployment $deployment, bool $flush = true): void;
    public function findById(string $id): ?Deployment;

    /** @return Deployment[] */
    public function findAll(int $limit, int $offset, ?string $providerId = null): array;
    public function countAll(?string $providerId = null): int;
}
