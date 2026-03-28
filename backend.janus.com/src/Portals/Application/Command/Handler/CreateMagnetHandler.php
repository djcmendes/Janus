<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\CreateMagnetCommand;
use App\Portals\Application\DTO\MagnetDto;
use App\Portals\Domain\Entity\Magnet;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
use App\Portals\Domain\ValueObject\SourceType;

final class CreateMagnetHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface  $portalRepository,
        private readonly MagnetRepositoryInterface  $magnetRepository,
    ) {}

    public function handle(CreateMagnetCommand $command): MagnetDto
    {
        $portal = $this->portalRepository->findById($command->portalId);
        if ($portal === null) {
            throw new PortalNotFoundException($command->portalId);
        }

        $magnet = new Magnet(
            portalId:           $command->portalId,
            name:               $command->name,
            sourceType:         SourceType::from($command->sourceType),
            targetCollectionId: $command->targetCollectionId,
            schedule:           $command->schedule,
        );

        $this->magnetRepository->save($magnet);

        return MagnetDto::fromEntity($magnet);
    }
}
