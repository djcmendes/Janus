<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;

use App\Portals\Domain\ValueObject\MagnetStatus;
use App\Portals\Domain\ValueObject\SourceConfig;
use App\Portals\Domain\ValueObject\SourceType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'magnets')]
final class Magnet
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'portal_id', type: 'string', length: 36)]
    private string $portalId;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(name: 'source_type', length: 100)]
    private string $sourceType;

    #[ORM\Column(name: 'source_config_json', type: 'json', nullable: true)]
    private ?array $sourceConfigJson = null;

    #[ORM\Column(name: 'target_collection_id', length: 255)]
    private string $targetCollectionId;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $schedule = null;

    #[ORM\Column(length: 50)]
    private string $status;

    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string     $portalId,
        string     $name,
        SourceType $sourceType,
        string     $targetCollectionId,
        ?string    $schedule = null,
    ) {
        $this->id                 = Uuid::v7()->toRfc4122();
        $this->portalId           = $portalId;
        $this->name               = $name;
        $this->sourceType         = $sourceType->value;
        $this->targetCollectionId = $targetCollectionId;
        $this->schedule           = $schedule;
        $this->status             = MagnetStatus::ACTIVE->value;
        $this->createdAt          = new \DateTimeImmutable();
    }

    public function updateSource(SourceType $sourceType, SourceConfig $config): void
    {
        $this->sourceType       = $sourceType->value;
        $this->sourceConfigJson = $config->toArray();
        $this->touch();
    }

    public function updateSchedule(?string $schedule): void
    {
        $this->schedule = $schedule;
        $this->touch();
    }

    public function rename(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function pause(): void
    {
        $this->status = MagnetStatus::PAUSED->value;
        $this->touch();
    }

    public function resume(): void
    {
        $this->status = MagnetStatus::ACTIVE->value;
        $this->touch();
    }

    public function archive(): void
    {
        $this->status = MagnetStatus::ARCHIVED->value;
        $this->touch();
    }

    public function isActive(): bool
    {
        return $this->status === MagnetStatus::ACTIVE->value;
    }

    public function getId(): string                    { return $this->id; }
    public function getPortalId(): string              { return $this->portalId; }
    public function getName(): string                  { return $this->name; }
    public function getSourceType(): SourceType        { return SourceType::from($this->sourceType); }
    public function getSourceConfig(): SourceConfig    { return SourceConfig::fromArray($this->sourceConfigJson ?? []); }
    public function getTargetCollectionId(): string    { return $this->targetCollectionId; }
    public function getSchedule(): ?string             { return $this->schedule; }
    public function getStatus(): MagnetStatus          { return MagnetStatus::from($this->status); }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
