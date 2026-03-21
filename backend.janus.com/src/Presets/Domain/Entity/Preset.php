<?php

declare(strict_types=1);

namespace App\Presets\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'presets')]
class Preset
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** The collection this preset applies to (nullable — global preset) */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $collection;

    /** Saved layout: e.g. "tabular", "cards", "map" */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $layout;

    /** JSON blob for layout options */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $layoutOptions;

    /** JSON blob for layout query (filter, sort, search, page, etc.) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $layoutQuery;

    /** Saved filter JSON */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $filter;

    /** Saved search string */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $search;

    /** Optional human-readable bookmark name */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bookmark;

    /** Owner — null means this is a default/global preset (admin-managed) */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $userId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        ?string $collection,
        ?string $layout,
        ?array  $layoutOptions,
        ?array  $layoutQuery,
        ?array  $filter,
        ?string $search,
        ?string $bookmark,
        ?string $userId,
    ) {
        $this->id            = Uuid::v7()->toRfc4122();
        $this->collection    = $collection;
        $this->layout        = $layout;
        $this->layoutOptions = $layoutOptions;
        $this->layoutQuery   = $layoutQuery;
        $this->filter        = $filter;
        $this->search        = $search;
        $this->bookmark      = $bookmark;
        $this->userId        = $userId;
        $this->createdAt     = new \DateTimeImmutable();
        $this->updatedAt     = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getCollection(): ?string { return $this->collection; }
    public function getLayout(): ?string { return $this->layout; }
    public function getLayoutOptions(): ?array { return $this->layoutOptions; }
    public function getLayoutQuery(): ?array { return $this->layoutQuery; }
    public function getFilter(): ?array { return $this->filter; }
    public function getSearch(): ?string { return $this->search; }
    public function getBookmark(): ?string { return $this->bookmark; }
    public function getUserId(): ?string { return $this->userId; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setCollection(?string $collection): static
    {
        $this->collection = $collection;
        $this->updatedAt  = new \DateTimeImmutable();
        return $this;
    }

    public function setLayout(?string $layout): static
    {
        $this->layout    = $layout;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setLayoutOptions(?array $layoutOptions): static
    {
        $this->layoutOptions = $layoutOptions;
        $this->updatedAt     = new \DateTimeImmutable();
        return $this;
    }

    public function setLayoutQuery(?array $layoutQuery): static
    {
        $this->layoutQuery = $layoutQuery;
        $this->updatedAt   = new \DateTimeImmutable();
        return $this;
    }

    public function setFilter(?array $filter): static
    {
        $this->filter    = $filter;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setSearch(?string $search): static
    {
        $this->search    = $search;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setBookmark(?string $bookmark): static
    {
        $this->bookmark  = $bookmark;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }
}
