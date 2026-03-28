<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class CreatePageRequest
{
    public function __construct(
        public readonly string  $title,
        public readonly string  $slug,
        public readonly ?string $parentId         = null,
        public readonly ?string $layoutTemplateId = null,
        public readonly array   $meta             = [],
        public readonly int     $sortOrder        = 0,
    ) {}
    public static function fromArray(array $data): self
    {
        if (empty($data['title'])) { throw new \InvalidArgumentException('title is required.'); }
        if (empty($data['slug']))  { throw new \InvalidArgumentException('slug is required.'); }
        return new self(
            title:            trim($data['title']),
            slug:             trim($data['slug']),
            parentId:         $data['parent_id']          ?? null,
            layoutTemplateId: $data['layout_template_id'] ?? null,
            meta:             $data['meta']                ?? [],
            sortOrder:        (int) ($data['sort_order']  ?? 0),
        );
    }
}
