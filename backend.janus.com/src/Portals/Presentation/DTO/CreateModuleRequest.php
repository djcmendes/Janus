<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class CreateModuleRequest
{
    public function __construct(
        public readonly string  $type,
        public readonly string  $name,
        public readonly array   $config   = [],
        public readonly ?string $portalId = null,
    ) {}
    public static function fromArray(array $data): self
    {
        if (empty($data['type'])) { throw new \InvalidArgumentException('type is required.'); }
        if (empty($data['name'])) { throw new \InvalidArgumentException('name is required.'); }
        return new self(
            type:     $data['type'],
            name:     trim($data['name']),
            config:   $data['config']    ?? [],
            portalId: $data['portal_id'] ?? null,
        );
    }
}
