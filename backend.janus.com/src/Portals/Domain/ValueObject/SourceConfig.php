<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;

/**
 * Typed wrapper around the per-source-type configuration JSON.
 *
 * RSS keys:     url, field_map
 * API keys:     url, method, auth_header, field_map
 * Webhook keys: secret
 */
final class SourceConfig
{
    private function __construct(private readonly array $data) {}

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function with(string $key, mixed $value): self
    {
        return new self(array_merge($this->data, [$key => $value]));
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
