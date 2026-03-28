<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
use Symfony\Component\Uid\Uuid;
final class PageId
{
    private function __construct(private readonly Uuid $value) {}
    public static function generate(): self { return new self(Uuid::v7()); }
    public static function fromString(string $id): self { return new self(Uuid::fromString($id)); }
    public function toString(): string { return (string) $this->value; }
    public function __toString(): string { return $this->toString(); }
    public function equals(self $other): bool { return $this->value->equals($other->value); }
}
