<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
final class Position
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $description = null,
    ) {}
    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) { throw new \InvalidArgumentException('Position name is required.'); }
        return new self(name: $data['name'], description: $data['description'] ?? null);
    }
    public function toArray(): array
    {
        return ['name' => $this->name, 'description' => $this->description];
    }
}
