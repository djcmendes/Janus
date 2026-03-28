<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\ModuleDto;
use App\Portals\Application\Query\ListModulesQuery;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
final class ListModulesHandler
{
    public function __construct(private readonly ModuleRepositoryInterface $repository) {}
    public function handle(ListModulesQuery $query): array
    {
        $modules = $this->repository->findPaginated($query->limit, $query->offset, $query->portalId);
        return [
            'data'  => array_map(fn ($m) => ModuleDto::fromEntity($m), $modules),
            'total' => $this->repository->countAll($query->portalId),
        ];
    }
}
