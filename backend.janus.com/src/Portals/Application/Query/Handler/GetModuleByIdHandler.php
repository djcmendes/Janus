<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\ModuleDto;
use App\Portals\Application\Query\GetModuleByIdQuery;
use App\Portals\Domain\Exception\ModuleNotFoundException;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
final class GetModuleByIdHandler
{
    public function __construct(private readonly ModuleRepositoryInterface $repository) {}
    public function handle(GetModuleByIdQuery $query): ModuleDto
    {
        $module = $this->repository->findById($query->id);
        if ($module === null) { throw new ModuleNotFoundException($query->id); }
        return ModuleDto::fromEntity($module);
    }
}
