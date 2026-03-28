<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\RemoveModuleCommand;
use App\Portals\Domain\Exception\PlacementNotFoundException;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
final class RemoveModuleHandler
{
    public function __construct(private readonly ModulePlacementRepositoryInterface $repository) {}
    public function handle(RemoveModuleCommand $command): void
    {
        $placement = $this->repository->findById($command->placementId);
        if ($placement === null) { throw new PlacementNotFoundException($command->placementId); }
        $this->repository->delete($placement);
    }
}
