<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\CreatePortalCommand;
use App\Portals\Application\DTO\PortalDto;
use App\Portals\Domain\Entity\Portal;
use App\Portals\Domain\Exception\PortalAlreadyExistsException;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
use App\Portals\Domain\ValueObject\PortalSettings;
use App\Portals\Domain\ValueObject\PortalStatus;
use App\Portals\Domain\ValueObject\Route;
final class CreatePortalHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}
    public function handle(CreatePortalCommand $command): PortalDto
    {
        $route = new Route($command->baseRoute);
        if ($this->repository->findByBaseRoute($route->toString()) !== null) {
            throw new PortalAlreadyExistsException($route->toString());
        }
        $portal = Portal::create(
            name:      $command->name,
            baseRoute: $route,
            status:    PortalStatus::from($command->status),
            settings:  PortalSettings::fromArray($command->settings),
        );
        $this->repository->save($portal);
        return PortalDto::fromEntity($portal);
    }
}
