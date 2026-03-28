<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\DeleteModuleCommand;
use App\Portals\Domain\Exception\ModuleNotFoundException;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
final class DeleteModuleHandler
{
    public function __construct(private readonly ModuleRepositoryInterface $repository) {}
    public function handle(DeleteModuleCommand $command): void
    {
        $module = $this->repository->findById($command->id);
        if ($module === null) { throw new ModuleNotFoundException($command->id); }
        $this->repository->delete($module);
    }
}
