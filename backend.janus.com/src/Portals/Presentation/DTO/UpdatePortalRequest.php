<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class UpdatePortalRequest
{
    public function __construct(
        public readonly ?string $name      = null,
        public readonly ?string $baseRoute = null,
        public readonly ?string $status    = null,
        public readonly ?array  $settings  = null,
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            name:      isset($data['name'])       ? trim($data['name'])       : null,
            baseRoute: isset($data['base_route']) ? trim($data['base_route']) : null,
            status:    $data['status']   ?? null,
            settings:  $data['settings'] ?? null,
        );
    }
}
