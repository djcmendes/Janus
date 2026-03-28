<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\Module;
final class ModuleDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $type,
        public readonly string  $name,
        public readonly array   $config,
        public readonly ?string $portalId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}
    public static function fromEntity(Module $module): self
    {
        return new self(
            id:        $module->getId(),
            type:      $module->getType()->value,
            name:      $module->getName(),
            config:    $module->getConfig()->toArray(),
            portalId:  $module->getPortalId(),
            createdAt: $module->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $module->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'name'       => $this->name,
            'config'     => $this->config,
            'portal_id'  => $this->portalId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
