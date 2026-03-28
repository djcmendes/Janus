<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class CreatePortalRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $baseRoute,
        public readonly string $status   = 'draft',
        public readonly array  $settings = [],
    ) {}
    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['name']))       { throw new \InvalidArgumentException('name is required.'); }
        if (empty($data['base_route'])) { throw new \InvalidArgumentException('base_route is required.'); }
        return new self(
            name:      trim($data['name']),
            baseRoute: trim($data['base_route']),
            status:    $data['status']   ?? 'draft',
            settings:  $data['settings'] ?? [],
        );
    }
}
