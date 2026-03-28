<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\ReorderModulesCommand;
use App\Portals\Domain\Exception\PlacementNotFoundException;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
final class ReorderModulesHandler
{
    public function __construct(
        private readonly ModulePlacementRepositoryInterface $repository,
        private readonly EntityManagerInterface             $em,
    ) {}
    public function handle(ReorderModulesCommand $command): void
    {
        foreach ($command->orderedItems as $item) {
            $placement = $this->repository->findById($item['id']);
            if ($placement === null) { throw new PlacementNotFoundException($item['id']); }
            $placement->updateSortOrder((int) $item['sort_order']);
            $this->repository->save($placement, false);
        }
        $this->em->flush();
    }
}
