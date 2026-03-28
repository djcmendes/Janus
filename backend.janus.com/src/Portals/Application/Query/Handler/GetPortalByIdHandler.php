<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\PortalDto;
use App\Portals\Application\Query\GetPortalByIdQuery;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
final class GetPortalByIdHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}
    public function handle(GetPortalByIdQuery $query): PortalDto
    {
        $portal = $this->repository->findById($query->id);
        if ($portal === null) { throw new PortalNotFoundException($query->id); }
        return PortalDto::fromEntity($portal);
    }
}
