<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
#[ORM\Entity]
#[ORM\Table(name: 'module_placements')]
final class ModulePlacement
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(name: 'page_id', type: 'string', length: 36)]
    private string $pageId;
    #[ORM\Column(name: 'position_name', length: 100)]
    private string $positionName;
    #[ORM\Column(name: 'module_id', type: 'string', length: 36)]
    private string $moduleId;
    #[ORM\Column(name: 'sort_order')]
    private int $sortOrder = 0;
    public function __construct(string $pageId, string $positionName, string $moduleId, int $sortOrder = 0)
    {
        $this->id           = (string) Uuid::v7();
        $this->pageId       = $pageId;
        $this->positionName = $positionName;
        $this->moduleId     = $moduleId;
        $this->sortOrder    = $sortOrder;
    }
    public function updateSortOrder(int $sortOrder): void { $this->sortOrder = $sortOrder; }
    public function getId(): string           { return $this->id; }
    public function getPageId(): string       { return $this->pageId; }
    public function getPositionName(): string { return $this->positionName; }
    public function getModuleId(): string     { return $this->moduleId; }
    public function getSortOrder(): int       { return $this->sortOrder; }
}
