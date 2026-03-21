<?php

declare(strict_types=1);

namespace App\Panels\Application\DTO;

use App\Panels\Domain\Entity\Panel;

final class PanelDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $dashboardId,
        public readonly string  $type,
        public readonly ?string $name,
        public readonly ?string $note,
        public readonly ?array  $options,
        public readonly int     $positionX,
        public readonly int     $positionY,
        public readonly int     $width,
        public readonly int     $height,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Panel $panel): self
    {
        return new self(
            id:          $panel->getId(),
            dashboardId: $panel->getDashboardId(),
            type:        $panel->getType(),
            name:        $panel->getName(),
            note:        $panel->getNote(),
            options:     $panel->getOptions(),
            positionX:   $panel->getPositionX(),
            positionY:   $panel->getPositionY(),
            width:       $panel->getWidth(),
            height:      $panel->getHeight(),
            createdAt:   $panel->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $panel->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
