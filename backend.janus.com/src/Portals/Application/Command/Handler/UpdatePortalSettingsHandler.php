<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\UpdatePortalSettingsCommand;
use App\Portals\Application\DTO\PortalDto;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
use App\Portals\Domain\ValueObject\PortalSettings;
use App\Portals\Domain\ValueObject\PortalStatus;
use App\Portals\Domain\ValueObject\Route;
final class UpdatePortalSettingsHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface $repository,
    ) {}
    public function handle(UpdatePortalSettingsCommand $command): PortalDto
    {
        $portal = $this->repository->findById($command->id);
        if ($portal === null) { throw new PortalNotFoundException($command->id); }
        if ($command->name !== null)      { $portal->rename($command->name); }
        if ($command->baseRoute !== null) { $portal->changeBaseRoute(new Route($command->baseRoute)); }
        if ($command->status !== null)    { $portal->changeStatus(PortalStatus::from($command->status)); }
        if ($command->settings !== null)  { $portal->updateSettings(PortalSettings::fromArray($command->settings)); }
        $this->repository->save($portal);
        return PortalDto::fromEntity($portal);
    }
}
