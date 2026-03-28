<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\ComponentDto;
use App\Portals\Application\Query\ListComponentsQuery;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
final class ListComponentsHandler
{
    public function __construct(private readonly ComponentRepositoryInterface $repository) {}
    public function handle(ListComponentsQuery $query): array
    {
        $items = $this->repository->findPaginated($query->limit, $query->offset);
        return [
            'data'  => array_map(fn ($c) => ComponentDto::fromEntity($c), $items),
            'total' => $this->repository->countAll(),
        ];
    }
}
