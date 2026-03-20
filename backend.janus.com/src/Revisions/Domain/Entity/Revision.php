<?php

declare(strict_types=1);

namespace App\Revisions\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Immutable snapshot of an item's state at a point in time.
 * Created programmatically by RevisionRecorder — never by user input.
 */
#[ORM\Entity]
#[ORM\Table(name: 'revisions')]
class Revision
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** The collection (table) this revision belongs to */
    #[ORM\Column(length: 200)]
    private string $collection;

    /** The item's UUID */
    #[ORM\Column(length: 255)]
    private string $item;

    /** Full snapshot of the item's field values */
    #[ORM\Column(type: 'json')]
    private array $data;

    /** Only the fields that changed from the previous revision (null on first revision) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $delta = null;

    /** Monotonically increasing version number per collection+item */
    #[ORM\Column]
    private int $version;

    /** UUID of the activity log entry that triggered this revision */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $activityId = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $collection,
        string $item,
        array  $data,
        int    $version,
        ?string $activityId = null,
    ) {
        $this->id         = Uuid::v7();
        $this->collection = $collection;
        $this->item       = $item;
        $this->data       = $data;
        $this->version    = $version;
        $this->activityId = $activityId;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getCollection(): string { return $this->collection; }
    public function getItem(): string { return $this->item; }
    public function getData(): array { return $this->data; }
    public function getDelta(): ?array { return $this->delta; }
    public function setDelta(?array $delta): static { $this->delta = $delta; return $this; }
    public function getVersion(): int { return $this->version; }
    public function getActivityId(): ?string { return $this->activityId; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
