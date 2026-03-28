<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;

use App\Portals\Application\DTO\MagnetDto;
use App\Portals\Application\DTO\MagnetListDto;
use App\Portals\Application\Query\ListMagnetsQuery;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;

final class ListMagnetsHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface $repository,
    ) {}

    public function handle(ListMagnetsQuery $query): MagnetListDto
    {
        $magnets = $this->repository->findByPortalId(
            $query->portalId,
            $query->limit,
            $query->offset,
        );

        return new MagnetListDto(
            data:  array_map(fn ($m) => MagnetDto::fromEntity($m), $magnets),
            total: $this->repository->countByPortalId($query->portalId),
        );
    }
}
