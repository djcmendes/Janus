<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
final class Slug
{
    private readonly string $value;
    public function __construct(string $value)
    {
        $trimmed = trim($value, '/');
        if ($trimmed === '' || !preg_match('/^[a-z0-9\-]+$/', $trimmed)) {
            throw new \InvalidArgumentException("Invalid slug: '{$value}'. Only lowercase letters, digits, and hyphens allowed.");
        }
        $this->value = $trimmed;
    }
    public function toString(): string { return $this->value; }
    public function __toString(): string { return $this->value; }
    public function equals(self $other): bool { return $this->value === $other->value; }
}
