<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
final class PortalSettings
{
    public function __construct(
        public readonly ?string $brandName    = null,
        public readonly ?string $logoUrl      = null,
        public readonly string  $timezone     = 'UTC',
        public readonly string  $locale       = 'en',
        public readonly ?string $primaryColor = null,
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            brandName:    $data['brand_name']    ?? null,
            logoUrl:      $data['logo_url']      ?? null,
            timezone:     $data['timezone']      ?? 'UTC',
            locale:       $data['locale']        ?? 'en',
            primaryColor: $data['primary_color'] ?? null,
        );
    }
    public function toArray(): array
    {
        return [
            'brand_name'    => $this->brandName,
            'logo_url'      => $this->logoUrl,
            'timezone'      => $this->timezone,
            'locale'        => $this->locale,
            'primary_color' => $this->primaryColor,
        ];
    }
}
