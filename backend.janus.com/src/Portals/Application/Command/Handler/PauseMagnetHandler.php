<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\PauseMagnetCommand;
use App\Portals\Application\DTO\MagnetDto;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\ValueObject\MagnetStatus;

final class PauseMagnetHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface $repository,
    ) {}

    public function handle(PauseMagnetCommand $command): MagnetDto
    {
        $magnet = $this->repository->findById($command->magnetId);
        if ($magnet === null) {
            throw new MagnetNotFoundException($command->magnetId);
        }

        if ($magnet->getStatus() === MagnetStatus::ACTIVE) {
            $magnet->pause();
        } else {
            $magnet->resume();
        }

        $this->repository->save($magnet);

        return MagnetDto::fromEntity($magnet);
    }
}
