<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\UpdateModuleConfigCommand;
use App\Portals\Application\DTO\ModuleDto;
use App\Portals\Domain\Exception\ModuleNotFoundException;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
use App\Portals\Domain\ValueObject\ModuleConfig;
final class UpdateModuleConfigHandler
{
    public function __construct(private readonly ModuleRepositoryInterface $repository) {}
    public function handle(UpdateModuleConfigCommand $command): ModuleDto
    {
        $module = $this->repository->findById($command->id);
        if ($module === null) { throw new ModuleNotFoundException($command->id); }
        $module->updateConfig($command->name, ModuleConfig::fromArray($command->config));
        $this->repository->save($module);
        return ModuleDto::fromEntity($module);
    }
}
