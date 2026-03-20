<?php

declare(strict_types=1);

namespace App\Translations\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'translations')]
#[ORM\UniqueConstraint(name: 'uniq_translations_language_key', columns: ['language', 'translation_key'])]
class Translation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** BCP 47 language tag, e.g. "en-US", "pt-BR" */
    #[ORM\Column(type: 'string', length: 16)]
    private string $language;

    /** Dot-notation key, e.g. "fields.title.label" */
    #[ORM\Column(name: 'translation_key', type: 'string', length: 255)]
    private string $key;

    #[ORM\Column(type: 'text')]
    private string $value;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $language,
        string $key,
        string $value,
    ) {
        $this->id        = Uuid::v7()->toRfc4122();
        $this->language  = $language;
        $this->key       = $key;
        $this->value     = $value;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getLanguage(): string { return $this->language; }
    public function getKey(): string { return $this->key; }
    public function getValue(): string { return $this->value; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setValue(string $value): static
    {
        $this->value     = $value;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
