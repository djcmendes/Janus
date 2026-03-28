<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\PlaceModuleCommand;
use App\Portals\Application\DTO\PlacementDto;
use App\Portals\Domain\Entity\ModulePlacement;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
final class PlaceModuleHandler
{
    public function __construct(private readonly ModulePlacementRepositoryInterface $repository) {}
    public function handle(PlaceModuleCommand $command): PlacementDto
    {
        $placement = new ModulePlacement($command->pageId, $command->positionName, $command->moduleId, $command->sortOrder);
        $this->repository->save($placement);
        return PlacementDto::fromEntity($placement);
    }
}
