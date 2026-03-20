<?php

declare(strict_types=1);

namespace App\Flows\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'operations')]
class Operation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** The flow this operation belongs to */
    #[ORM\Column(type: 'string', length: 36)]
    private string $flowId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /**
     * Operation type key — determines what the operation does.
     * Examples: "log", "send_email", "request", "create_item", "update_item",
     *           "delete_item", "read_data", "transform_data", "condition"
     */
    #[ORM\Column(type: 'string', length: 64)]
    private string $type;

    /** Type-specific configuration (JSON) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options;

    /** Key in the flow data payload to resolve as input for this operation */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resolve;

    /** ID of next operation on success path — null = flow ends */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $nextSuccess;

    /** ID of next operation on failure/reject path — null = flow ends */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $nextFailure;

    /** Execution order within the flow */
    #[ORM\Column(type: 'integer')]
    private int $sortOrder;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string  $flowId,
        string  $name,
        string  $type,
        ?array  $options     = null,
        ?string $resolve     = null,
        ?string $nextSuccess = null,
        ?string $nextFailure = null,
        int     $sortOrder   = 0,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->flowId      = $flowId;
        $this->name        = $name;
        $this->type        = $type;
        $this->options     = $options;
        $this->resolve     = $resolve;
        $this->nextSuccess = $nextSuccess;
        $this->nextFailure = $nextFailure;
        $this->sortOrder   = $sortOrder;
        $this->createdAt   = new \DateTimeImmutable();
        $this->updatedAt   = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getFlowId(): string { return $this->flowId; }
    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getOptions(): ?array { return $this->options; }
    public function getResolve(): ?string { return $this->resolve; }
    public function getNextSuccess(): ?string { return $this->nextSuccess; }
    public function getNextFailure(): ?string { return $this->nextFailure; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setName(string $name): static
    {
        $this->name      = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setType(string $type): static
    {
        $this->type      = $type;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setOptions(?array $options): static
    {
        $this->options   = $options;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setResolve(?string $resolve): static
    {
        $this->resolve   = $resolve;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setNextSuccess(?string $id): static
    {
        $this->nextSuccess = $id;
        $this->updatedAt   = new \DateTimeImmutable();
        return $this;
    }

    public function setNextFailure(?string $id): static
    {
        $this->nextFailure = $id;
        $this->updatedAt   = new \DateTimeImmutable();
        return $this;
    }

    public function setSortOrder(int $order): static
    {
        $this->sortOrder = $order;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
