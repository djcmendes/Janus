<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\Page;
final class PageDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $portalId,
        public readonly ?string $parentId,
        public readonly string  $slug,
        public readonly string  $fullPath,
        public readonly string  $title,
        public readonly ?string $layoutTemplateId,
        public readonly ?string $centerComponentId,
        public readonly ?string $customCss,
        public readonly array   $meta,
        public readonly string  $status,
        public readonly int     $sortOrder,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}
    public static function fromEntity(Page $page): self
    {
        return new self(
            id:                $page->getId(),
            portalId:          $page->getPortalId(),
            parentId:          $page->getParentId(),
            slug:              $page->getSlug()->toString(),
            fullPath:          $page->getFullPath(),
            title:             $page->getTitle(),
            layoutTemplateId:  $page->getLayoutTemplateId(),
            centerComponentId: $page->getCenterComponentId(),
            customCss:         $page->getCustomCss(),
            meta:              $page->getMeta()->toArray(),
            status:            $page->getStatus()->value,
            sortOrder:         $page->getSortOrder(),
            createdAt:         $page->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:         $page->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'portal_id'           => $this->portalId,
            'parent_id'           => $this->parentId,
            'slug'                => $this->slug,
            'full_path'           => $this->fullPath,
            'title'               => $this->title,
            'layout_template_id'  => $this->layoutTemplateId,
            'center_component_id' => $this->centerComponentId,
            'custom_css'          => $this->customCss,
            'meta'                => $this->meta,
            'status'              => $this->status,
            'sort_order'          => $this->sortOrder,
            'created_at'          => $this->createdAt,
            'updated_at'          => $this->updatedAt,
        ];
    }
}
