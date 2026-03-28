<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\LayoutTemplate;
final class LayoutTemplateDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly array   $positions,
        public readonly string  $templateMarkup,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}
    public static function fromEntity(LayoutTemplate $template): self
    {
        return new self(
            id:             $template->getId(),
            name:           $template->getName(),
            positions:      array_map(fn ($p) => $p->toArray(), $template->getPositions()),
            templateMarkup: $template->getTemplateMarkup(),
            createdAt:      $template->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:      $template->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'positions'       => $this->positions,
            'template_markup' => $this->templateMarkup,
            'created_at'      => $this->createdAt,
            'updated_at'      => $this->updatedAt,
        ];
    }
}
