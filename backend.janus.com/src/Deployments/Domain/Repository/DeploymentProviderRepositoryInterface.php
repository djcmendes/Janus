<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Repository;

use App\Deployments\Domain\Entity\DeploymentProvider;

interface DeploymentProviderRepositoryInterface
{
    public function save(DeploymentProvider $provider, bool $flush = true): void;
    public function delete(DeploymentProvider $provider): void;
    public function findById(string $id): ?DeploymentProvider;

    /** @return DeploymentProvider[] */
    public function findPaginated(int $limit, int $offset): array;
    public function countAll(): int;
}
