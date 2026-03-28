<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use App\Portals\Domain\ValueObject\PageId;
use App\Portals\Domain\ValueObject\PageMeta;
use App\Portals\Domain\ValueObject\PageStatus;
use App\Portals\Domain\ValueObject\Slug;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'pages')]
final class Page
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(name: 'portal_id', type: 'string', length: 36)]
    private string $portalId;
    #[ORM\Column(name: 'parent_id', type: 'string', length: 36, nullable: true)]
    private ?string $parentId = null;
    #[ORM\Column(length: 255)]
    private string $slug;
    #[ORM\Column(name: 'full_path', length: 1024)]
    private string $fullPath;
    #[ORM\Column(length: 255)]
    private string $title;
    #[ORM\Column(name: 'layout_template_id', type: 'string', length: 36, nullable: true)]
    private ?string $layoutTemplateId = null;
    #[ORM\Column(name: 'center_component_id', type: 'string', length: 36, nullable: true)]
    private ?string $centerComponentId = null;
    #[ORM\Column(name: 'custom_css', type: 'text', nullable: true)]
    private ?string $customCss = null;
    #[ORM\Column(name: 'meta_json', type: 'json', nullable: true)]
    private ?array $metaJson = null;
    #[ORM\Column(length: 50)]
    private string $status;
    #[ORM\Column(name: 'sort_order')]
    private int $sortOrder = 0;
    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string  $portalId,
        string  $title,
        Slug    $slug,
        string  $fullPath,
        ?string $parentId = null,
    ) {
        $this->id        = PageId::generate()->toString();
        $this->portalId  = $portalId;
        $this->parentId  = $parentId;
        $this->slug      = $slug->toString();
        $this->fullPath  = $fullPath;
        $this->title     = $title;
        $this->status    = PageStatus::DRAFT->value;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function updateBasicInfo(string $title, Slug $slug, string $fullPath): void
    {
        $this->title    = $title;
        $this->slug     = $slug->toString();
        $this->fullPath = $fullPath;
        $this->touch();
    }

    public function moveTo(?string $parentId, string $newFullPath): void
    {
        $this->parentId = $parentId;
        $this->fullPath = $newFullPath;
        $this->touch();
    }

    public function setLayoutTemplate(?string $layoutTemplateId): void
    {
        $this->layoutTemplateId = $layoutTemplateId;
        $this->touch();
    }

    public function setCenterComponent(?string $componentId): void
    {
        $this->centerComponentId = $componentId;
        $this->touch();
    }

    public function setCustomCss(?string $css): void
    {
        $this->customCss = $css;
        $this->touch();
    }

    public function setMeta(PageMeta $meta): void
    {
        $this->metaJson = $meta->toArray();
        $this->touch();
    }

    public function publish(): void   { $this->status = PageStatus::PUBLISHED->value; $this->touch(); }
    public function unpublish(): void { $this->status = PageStatus::DRAFT->value;     $this->touch(); }
    public function archive(): void   { $this->status = PageStatus::ARCHIVED->value;  $this->touch(); }

    public function setSortOrder(int $order): void { $this->sortOrder = $order; $this->touch(); }

    public function getId(): string                    { return $this->id; }
    public function getPortalId(): string              { return $this->portalId; }
    public function getParentId(): ?string             { return $this->parentId; }
    public function getSlug(): Slug                    { return new Slug($this->slug); }
    public function getFullPath(): string              { return $this->fullPath; }
    public function getTitle(): string                 { return $this->title; }
    public function getLayoutTemplateId(): ?string     { return $this->layoutTemplateId; }
    public function getCenterComponentId(): ?string    { return $this->centerComponentId; }
    public function getCustomCss(): ?string            { return $this->customCss; }
    public function getMeta(): PageMeta                { return PageMeta::fromArray($this->metaJson ?? []); }
    public function getStatus(): PageStatus            { return PageStatus::from($this->status); }
    public function getSortOrder(): int                { return $this->sortOrder; }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
