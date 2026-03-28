<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\CreateComponentCommand;
use App\Portals\Application\DTO\ComponentDto;
use App\Portals\Domain\Entity\ComponentDefinition;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
use App\Portals\Domain\ValueObject\ComponentType;
use App\Portals\Domain\ValueObject\QueryConfig;
use App\Portals\Domain\ValueObject\RenderConfig;
final class CreateComponentHandler
{
    public function __construct(private readonly ComponentRepositoryInterface $repository) {}
    public function handle(CreateComponentCommand $command): ComponentDto
    {
        $component = new ComponentDefinition(ComponentType::from($command->type), $command->collectionId);
        $component->update($command->collectionId, QueryConfig::fromArray($command->queryConfig), RenderConfig::fromArray($command->renderConfig));
        $this->repository->save($component);
        return ComponentDto::fromEntity($component);
    }
}
