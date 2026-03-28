<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\LayoutTemplateDto;
use App\Portals\Application\Query\GetLayoutTemplateByIdQuery;
use App\Portals\Domain\Exception\LayoutTemplateNotFoundException;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
final class GetLayoutTemplateByIdHandler
{
    public function __construct(
        private readonly LayoutTemplateRepositoryInterface $repository,
    ) {}
    public function handle(GetLayoutTemplateByIdQuery $query): LayoutTemplateDto
    {
        $template = $this->repository->findById($query->id);
        if ($template === null) { throw new LayoutTemplateNotFoundException($query->id); }
        return LayoutTemplateDto::fromEntity($template);
    }
}
