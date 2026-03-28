<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\PortalDto;
use App\Portals\Application\DTO\PortalListDto;
use App\Portals\Application\Query\ListPortalsQuery;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
final class ListPortalsHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}
    public function handle(ListPortalsQuery $query): PortalListDto
    {
        $portals = $this->repository->findPaginated($query->limit, $query->offset);
        return new PortalListDto(
            data:  array_map(fn ($p) => PortalDto::fromEntity($p), $portals),
            total: $this->repository->countAll(),
        );
    }
}
