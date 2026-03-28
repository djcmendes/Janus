<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;
use App\Portals\Application\DTO\ComponentDto;
use App\Portals\Application\DTO\LayoutTemplateDto;
use App\Portals\Application\DTO\ModuleDto;
use App\Portals\Application\DTO\PageDto;
use App\Portals\Application\DTO\PageLayoutDto;
use App\Portals\Application\DTO\PlacementDto;
use App\Portals\Application\Query\GetPageWithLayoutQuery;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Domain\Repository\ComponentRepositoryInterface;
use App\Portals\Domain\Repository\LayoutTemplateRepositoryInterface;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
use App\Portals\Domain\Repository\ModuleRepositoryInterface;
use App\Portals\Domain\Repository\PageRepositoryInterface;
final class GetPageWithLayoutHandler
{
    public function __construct(
        private readonly PageRepositoryInterface             $pageRepo,
        private readonly LayoutTemplateRepositoryInterface   $layoutRepo,
        private readonly ModulePlacementRepositoryInterface  $placementRepo,
        private readonly ModuleRepositoryInterface           $moduleRepo,
        private readonly ComponentRepositoryInterface        $componentRepo,
    ) {}
    public function handle(GetPageWithLayoutQuery $query): PageLayoutDto
    {
        $page = $this->pageRepo->findById($query->pageId);
        if ($page === null) { throw new PageNotFoundException($query->pageId); }
        $pageDto = PageDto::fromEntity($page);
        // Layout template
        $layoutTemplate = null;
        if ($page->getLayoutTemplateId() !== null) {
            $template = $this->layoutRepo->findById($page->getLayoutTemplateId());
            if ($template !== null) {
                $layoutTemplate = LayoutTemplateDto::fromEntity($template)->toArray();
            }
        }
        // Placements grouped by position
        $placements = $this->placementRepo->findByPage($page->getId());
        $positions  = [];
        foreach ($placements as $placement) {
            $module = $this->moduleRepo->findById($placement->getModuleId());
            $positions[$placement->getPositionName()][] = [
                'placement' => PlacementDto::fromEntity($placement)->toArray(),
                'module'    => $module !== null ? ModuleDto::fromEntity($module)->toArray() : null,
            ];
        }
        // Center component
        $centerComponent = null;
        if ($page->getCenterComponentId() !== null) {
            $component = $this->componentRepo->findById($page->getCenterComponentId());
            if ($component !== null) {
                $centerComponent = ComponentDto::fromEntity($component)->toArray();
            }
        }
        return new PageLayoutDto($pageDto, $layoutTemplate, $positions, $centerComponent);
    }
}
