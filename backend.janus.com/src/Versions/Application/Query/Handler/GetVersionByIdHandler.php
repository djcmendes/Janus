<?php

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionByIdQuery;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

final class GetVersionByIdHandler
{
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /** @throws VersionNotFoundException */
    public function handle(GetVersionByIdQuery $query): VersionDto
    {
        $version = $this->repository->findById($query->id);

        if ($version === null) {
            throw new VersionNotFoundException($query->id);
        }

        return VersionDto::fromEntity($version);
    }
}
