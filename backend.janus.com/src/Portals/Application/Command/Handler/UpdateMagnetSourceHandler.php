<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\UpdateMagnetSourceCommand;
use App\Portals\Application\DTO\MagnetDto;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\ValueObject\SourceConfig;
use App\Portals\Domain\ValueObject\SourceType;

final class UpdateMagnetSourceHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface $repository,
    ) {}

    public function handle(UpdateMagnetSourceCommand $command): MagnetDto
    {
        $magnet = $this->repository->findById($command->magnetId);
        if ($magnet === null) {
            throw new MagnetNotFoundException($command->magnetId);
        }

        if ($command->sourceType !== null) {
            $magnet->updateSource(
                SourceType::from($command->sourceType),
                SourceConfig::fromArray($command->sourceConfig ?? []),
            );
        }

        if ($command->name !== null) {
            $magnet->rename($command->name);
        }

        if ($command->schedule !== null) {
            $magnet->updateSchedule($command->schedule);
        }

        $this->repository->save($magnet);

        return MagnetDto::fromEntity($magnet);
    }
}
