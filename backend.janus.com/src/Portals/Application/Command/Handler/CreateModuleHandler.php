<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\CreateModuleCommand;
use App\Portals\Application\DTO\ModuleDto;
use App\Portals\Domain\Entity\Module;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
use App\Portals\Domain\ValueObject\ModuleType;
final class CreateModuleHandler
{
    public function __construct(private readonly ModuleRepositoryInterface $repository) {}
    public function handle(CreateModuleCommand $command): ModuleDto
    {
        $module = new Module(ModuleType::from($command->type), $command->name, $command->portalId);
        if (!empty($command->config)) {
            $module->updateConfig($command->name, \App\Portals\Domain\ValueObject\ModuleConfig::fromArray($command->config));
        }
        $this->repository->save($module);
        return ModuleDto::fromEntity($module);
    }
}
