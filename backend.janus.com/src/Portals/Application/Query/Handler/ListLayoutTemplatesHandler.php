<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\LayoutTemplateDto;
use App\Portals\Application\Query\ListLayoutTemplatesQuery;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
final class ListLayoutTemplatesHandler
{
    public function __construct(
        private readonly LayoutTemplateRepositoryInterface $repository,
    ) {}
    public function handle(ListLayoutTemplatesQuery $query): array
    {
        $templates = $this->repository->findPaginated($query->limit, $query->offset);
        return [
            'data'  => array_map(fn ($t) => LayoutTemplateDto::fromEntity($t), $templates),
            'total' => $this->repository->countAll(),
        ];
    }
}
