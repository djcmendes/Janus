<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\DeleteComponentCommand;
use App\Portals\Domain\Exception\ComponentNotFoundException;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
final class DeleteComponentHandler
{
    public function __construct(private readonly ComponentRepositoryInterface $repository) {}
    public function handle(DeleteComponentCommand $command): void
    {
        $component = $this->repository->findById($command->id);
        if ($component === null) { throw new ComponentNotFoundException($command->id); }
        $this->repository->delete($component);
    }
}
