<?php

declare(strict_types=1);

namespace App\Fields\Domain\Entity;

use App\Fields\Domain\Enum\FieldType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'janus_fields')]
#[ORM\UniqueConstraint(name: 'UNIQ_FIELD_COLLECTION_FIELD', columns: ['collection', 'field'])]
class FieldMeta
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** The collection (table) this field belongs to */
    #[ORM\Column(length: 64)]
    private string $collection;

    /** The column name inside that table */
    #[ORM\Column(length: 64)]
    private string $field;

    #[ORM\Column(length: 30, enumType: FieldType::class)]
    private FieldType $type;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    #[ORM\Column]
    private bool $required = false;

    #[ORM\Column]
    private bool $readonly = false;

    #[ORM\Column]
    private bool $hidden = false;

    #[ORM\Column]
    private int $sortOrder = 0;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $interface = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(string $collection, string $field, FieldType $type)
    {
        $this->id         = Uuid::v7();
        $this->collection = $collection;
        $this->field      = $field;
        $this->type       = $type;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getCollection(): string { return $this->collection; }
    public function getField(): string { return $this->field; }

    public function getType(): FieldType { return $this->type; }
    public function setType(FieldType $type): static { $this->type = $type; return $this->touch(); }

    public function getLabel(): ?string { return $this->label; }
    public function setLabel(?string $label): static { $this->label = $label; return $this->touch(); }

    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): static { $this->note = $note; return $this->touch(); }

    public function isRequired(): bool { return $this->required; }
    public function setRequired(bool $required): static { $this->required = $required; return $this->touch(); }

    public function isReadonly(): bool { return $this->readonly; }
    public function setReadonly(bool $readonly): static { $this->readonly = $readonly; return $this->touch(); }

    public function isHidden(): bool { return $this->hidden; }
    public function setHidden(bool $hidden): static { $this->hidden = $hidden; return $this->touch(); }

    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this->touch(); }

    public function getInterface(): ?string { return $this->interface; }
    public function setInterface(?string $interface): static { $this->interface = $interface; return $this->touch(); }

    public function getOptions(): ?array { return $this->options; }
    public function setOptions(?array $options): static { $this->options = $options; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
