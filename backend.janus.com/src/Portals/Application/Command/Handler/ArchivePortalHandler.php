<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\ArchivePortalCommand;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
final class ArchivePortalHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}
    public function handle(ArchivePortalCommand $command): void
    {
        $portal = $this->repository->findById($command->id);
        if ($portal === null) { throw new PortalNotFoundException($command->id); }
        $portal->archive();
        $this->repository->save($portal);
    }
}
