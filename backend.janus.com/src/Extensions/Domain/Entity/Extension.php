<?php

declare(strict_types=1);

namespace App\Extensions\Domain\Entity;

use App\Extensions\Domain\Enum\ExtensionType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'extensions')]
#[ORM\UniqueConstraint(name: 'uniq_extensions_name_type', columns: ['name', 'type'])]
class Extension
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** Package/bundle name — unique per type */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 16, enumType: ExtensionType::class)]
    private ExtensionType $type;

    #[ORM\Column(type: 'string', length: 64)]
    private string $version;

    /** Whether this extension is currently active */
    #[ORM\Column(type: 'boolean')]
    private bool $enabled;

    /** Optional human-readable description */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    /** Entry-point configuration (JSON) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $meta;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string        $name,
        ExtensionType $type,
        string        $version,
        bool          $enabled     = false,
        ?string       $description = null,
        ?array        $meta        = null,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->name        = $name;
        $this->type        = $type;
        $this->version     = $version;
        $this->enabled     = $enabled;
        $this->description = $description;
        $this->meta        = $meta;
        $this->createdAt   = new \DateTimeImmutable();
        $this->updatedAt   = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getType(): ExtensionType { return $this->type; }
    public function getVersion(): string { return $this->version; }
    public function isEnabled(): bool { return $this->enabled; }
    public function getDescription(): ?string { return $this->description; }
    public function getMeta(): ?array { return $this->meta; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled   = $enabled;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setVersion(string $version): static
    {
        $this->version   = $version;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setMeta(?array $meta): static
    {
        $this->meta      = $meta;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
