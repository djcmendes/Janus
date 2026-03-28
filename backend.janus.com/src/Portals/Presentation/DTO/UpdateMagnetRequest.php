<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;

final class UpdateMagnetRequest
{
    public function __construct(
        public readonly ?string $name         = null,
        public readonly ?string $sourceType   = null,
        public readonly ?array  $sourceConfig = null,
        public readonly ?string $schedule     = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:         isset($data['name'])          ? trim($data['name'])              : null,
            sourceType:   isset($data['source_type'])   ? (string) $data['source_type']    : null,
            sourceConfig: isset($data['source_config']) ? (array)  $data['source_config']  : null,
            schedule:     array_key_exists('schedule', $data) ? ($data['schedule'] === null ? null : (string) $data['schedule']) : null,
        );
    }
}
