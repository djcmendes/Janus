<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
final class PageLayoutDto
{
    public function __construct(
        public readonly PageDto     $page,
        public readonly ?array      $layoutTemplate,
        public readonly array       $positions,
        public readonly ?array      $centerComponent,
    ) {}
    public function toArray(): array
    {
        return [
            'page'            => $this->page->toArray(),
            'layout_template' => $this->layoutTemplate,
            'positions'       => $this->positions,
            'center_component' => $this->centerComponent,
        ];
    }
}
