<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\Portal;
final class PortalDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $baseRoute,
        public readonly string  $status,
        public readonly array   $settings,
        public readonly ?string $portalCss,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}
    public static function fromEntity(Portal $portal): self
    {
        return new self(
            id:        $portal->getId()->toString(),
            name:      $portal->getName(),
            baseRoute: $portal->getBaseRoute()->toString(),
            status:    $portal->getStatus()->value,
            settings:  $portal->getSettings()->toArray(),
            portalCss: $portal->getPortalCss(),
            createdAt: $portal->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $portal->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'base_route' => $this->baseRoute,
            'status'     => $this->status,
            'settings'   => $this->settings,
            'portal_css' => $this->portalCss,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
