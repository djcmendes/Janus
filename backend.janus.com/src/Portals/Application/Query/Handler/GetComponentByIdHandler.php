<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\ComponentDto;
use App\Portals\Application\Query\GetComponentByIdQuery;
use App\Portals\Domain\Exception\ComponentNotFoundException;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
final class GetComponentByIdHandler
{
    public function __construct(private readonly ComponentRepositoryInterface $repository) {}
    public function handle(GetComponentByIdQuery $query): ComponentDto
    {
        $component = $this->repository->findById($query->id);
        if ($component === null) { throw new ComponentNotFoundException($query->id); }
        return ComponentDto::fromEntity($component);
    }
}
