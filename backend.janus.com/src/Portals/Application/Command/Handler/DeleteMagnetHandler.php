<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\DeleteMagnetCommand;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;

final class DeleteMagnetHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface $repository,
    ) {}

    public function handle(DeleteMagnetCommand $command): void
    {
        $magnet = $this->repository->findById($command->magnetId);
        if ($magnet === null) {
            throw new MagnetNotFoundException($command->magnetId);
        }

        $this->repository->delete($magnet);
    }
}
