<?php
declare(strict_types=1);
namespace App\Portals\Application\Command\Handler;
use App\Portals\Application\Command\UpdateComponentCommand;
use App\Portals\Application\DTO\ComponentDto;
use App\Portals\Domain\Exception\ComponentNotFoundException;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
use App\Portals\Domain\ValueObject\QueryConfig;
use App\Portals\Domain\ValueObject\RenderConfig;
final class UpdateComponentHandler
{
    public function __construct(private readonly ComponentRepositoryInterface $repository) {}
    public function handle(UpdateComponentCommand $command): ComponentDto
    {
        $component = $this->repository->findById($command->id);
        if ($component === null) { throw new ComponentNotFoundException($command->id); }
        $component->update($command->collectionId, QueryConfig::fromArray($command->queryConfig), RenderConfig::fromArray($command->renderConfig));
        $this->repository->save($component);
        return ComponentDto::fromEntity($component);
    }
}
