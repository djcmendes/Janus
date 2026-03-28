<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\Page;
final class PageTreeNodeDto
{
    /** @param PageTreeNodeDto[] $children */
    public function __construct(
        public readonly string  $id,
        public readonly ?string $parentId,
        public readonly string  $slug,
        public readonly string  $fullPath,
        public readonly string  $title,
        public readonly string  $status,
        public readonly int     $sortOrder,
        public readonly array   $children = [],
    ) {}
    public static function fromEntity(Page $page, array $children = []): self
    {
        return new self(
            id:        $page->getId(),
            parentId:  $page->getParentId(),
            slug:      $page->getSlug()->toString(),
            fullPath:  $page->getFullPath(),
            title:     $page->getTitle(),
            status:    $page->getStatus()->value,
            sortOrder: $page->getSortOrder(),
            children:  $children,
        );
    }
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'parent_id'  => $this->parentId,
            'slug'       => $this->slug,
            'full_path'  => $this->fullPath,
            'title'      => $this->title,
            'status'     => $this->status,
            'sort_order' => $this->sortOrder,
            'children'   => array_map(fn ($c) => $c->toArray(), $this->children),
        ];
    }
}
