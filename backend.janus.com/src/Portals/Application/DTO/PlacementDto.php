<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\ModulePlacement;
final class PlacementDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $pageId,
        public readonly string $positionName,
        public readonly string $moduleId,
        public readonly int    $sortOrder,
    ) {}
    public static function fromEntity(ModulePlacement $p): self
    {
        return new self($p->getId(), $p->getPageId(), $p->getPositionName(), $p->getModuleId(), $p->getSortOrder());
    }
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'page_id'       => $this->pageId,
            'position_name' => $this->positionName,
            'module_id'     => $this->moduleId,
            'sort_order'    => $this->sortOrder,
        ];
    }
}
