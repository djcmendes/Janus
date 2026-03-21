<?php

declare(strict_types=1);

namespace App\Flows\Domain\Entity;

use App\Flows\Domain\Enum\FlowStatus;
use App\Flows\Domain\Enum\TriggerType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'flows')]
class Flow
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 10, enumType: FlowStatus::class)]
    private FlowStatus $status;

    #[ORM\Column(name: '`trigger`', type: 'string', length: 16, enumType: TriggerType::class)]
    private TriggerType $trigger;

    /** JSON blob: trigger-specific configuration (e.g. collection+action for ACTION, cron expr for SCHEDULE) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $triggerOptions;

    /** Optional accountability: user who created/owns this flow */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $userId;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string      $name,
        FlowStatus  $status,
        TriggerType $trigger,
        ?array      $triggerOptions = null,
        ?string     $userId         = null,
        ?string     $description    = null,
    ) {
        $this->id             = Uuid::v7()->toRfc4122();
        $this->name           = $name;
        $this->status         = $status;
        $this->trigger        = $trigger;
        $this->triggerOptions = $triggerOptions;
        $this->userId         = $userId;
        $this->description    = $description;
        $this->createdAt      = new \DateTimeImmutable();
        $this->updatedAt      = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getStatus(): FlowStatus { return $this->status; }
    public function getTrigger(): TriggerType { return $this->trigger; }
    public function getTriggerOptions(): ?array { return $this->triggerOptions; }
    public function getUserId(): ?string { return $this->userId; }
    public function getDescription(): ?string { return $this->description; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setName(string $name): static
    {
        $this->name      = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setStatus(FlowStatus $status): static
    {
        $this->status    = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setTrigger(TriggerType $trigger): static
    {
        $this->trigger   = $trigger;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setTriggerOptions(?array $options): static
    {
        $this->triggerOptions = $options;
        $this->updatedAt      = new \DateTimeImmutable();
        return $this;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        $this->updatedAt   = new \DateTimeImmutable();
        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === FlowStatus::ACTIVE;
    }
}
