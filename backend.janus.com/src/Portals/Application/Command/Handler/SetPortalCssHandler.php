<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;

use App\Portals\Application\Command\SetPortalCssCommand;
use App\Portals\Application\DTO\PortalDto;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\PortalRepositoryInterface;

final class SetPortalCssHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}

    public function handle(SetPortalCssCommand $command): PortalDto
    {
        $portal = $this->repository->findById($command->portalId);
        if ($portal === null) {
            throw new PortalNotFoundException($command->portalId);
        }

        $portal->setPortalCss($command->css);
        $this->repository->save($portal);

        return PortalDto::fromEntity($portal);
    }
}
