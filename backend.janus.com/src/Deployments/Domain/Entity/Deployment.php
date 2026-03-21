<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Entity;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * A deployment run — records the outcome of triggering a DeploymentProvider.
 */
#[ORM\Entity]
#[ORM\Table(name: 'deployments')]
#[ORM\Index(name: 'idx_deployment_provider', columns: ['provider_id'])]
class Deployment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** UUID of the DeploymentProvider — stored as string, no ORM FK */
    #[ORM\Column(length: 36)]
    private string $providerId;

    #[ORM\Column(length: 20, enumType: DeploymentRunStatus::class)]
    private DeploymentRunStatus $status;

    /** Response body / error message from the provider */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $log = null;

    /** UUID of the user who triggered this run */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $triggeredBy = null;

    #[ORM\Column]
    private \DateTimeImmutable $startedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(string $providerId, ?string $triggeredBy = null)
    {
        $this->id          = Uuid::v7();
        $this->providerId  = $providerId;
        $this->status      = DeploymentRunStatus::PENDING;
        $this->triggeredBy = $triggeredBy;
        $this->startedAt   = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getProviderId(): string { return $this->providerId; }

    public function getStatus(): DeploymentRunStatus { return $this->status; }
    public function setStatus(DeploymentRunStatus $status): static { $this->status = $status; return $this; }

    public function getLog(): ?string { return $this->log; }
    public function setLog(?string $log): static { $this->log = $log; return $this; }

    public function getTriggeredBy(): ?string { return $this->triggeredBy; }
    public function getStartedAt(): \DateTimeImmutable { return $this->startedAt; }

    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }
    public function complete(DeploymentRunStatus $status, ?string $log = null): static
    {
        $this->status      = $status;
        $this->log         = $log;
        $this->completedAt = new \DateTimeImmutable();
        return $this;
    }
}
