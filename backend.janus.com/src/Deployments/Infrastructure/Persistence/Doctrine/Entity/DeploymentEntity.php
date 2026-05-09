<?php

/**
 * @file DeploymentEntity.php
 *
 * Doctrine ORM persistence model for the `deployments` table.
 * This class is the sole owner of all database-mapping concerns for deployment run records.
 * Domain logic lives exclusively in Deployment (Domain\Entity).
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping deployment run records to the `deployments` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Deployment class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'deployments')]
#[ORM\Index(name: 'idx_deployment_provider', columns: ['provider_id'])]
class DeploymentEntity
{
    /** @var Uuid UUID primary key (stored as uuid column type). */
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    /** @var string UUID of the DeploymentProvider — stored as string, no ORM FK. */
    #[ORM\Column(length: 36)]
    private string $providerId;

    /** @var DeploymentRunStatus Current lifecycle state. */
    #[ORM\Column(length: 20, enumType: DeploymentRunStatus::class)]
    private DeploymentRunStatus $status;

    /** @var string|null Response body or error message from the provider. */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $log = null;

    /** @var string|null UUID of the user who triggered this run. */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $triggeredBy = null;

    /** @var \DateTimeImmutable UTC start timestamp. */
    #[ORM\Column]
    private \DateTimeImmutable $startedAt;

    /** @var \DateTimeImmutable|null UTC completion timestamp, or null when still running. */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    /**
     * Returns the UUID primary key.
     *
     * @return Uuid Doctrine-managed UUID value object.
     */
    public function getId(): Uuid { return $this->id; }

    /**
     * Sets the UUID primary key.
     *
     * @param  Uuid   $id UUID value object.
     * @return static      Fluent self for chaining.
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * Returns the provider UUID string.
     *
     * @return string Provider UUID.
     */
    public function getProviderId(): string { return $this->providerId; }

    /**
     * Sets the provider UUID string.
     *
     * @param  string $providerId Provider UUID.
     * @return static              Fluent self for chaining.
     */
    public function setProviderId(string $providerId): static { $this->providerId = $providerId; return $this; }

    /**
     * Returns the current lifecycle status.
     *
     * @return DeploymentRunStatus Enum case.
     */
    public function getStatus(): DeploymentRunStatus { return $this->status; }

    /**
     * Sets the lifecycle status.
     *
     * @param  DeploymentRunStatus $status New status.
     * @return static                       Fluent self for chaining.
     */
    public function setStatus(DeploymentRunStatus $status): static { $this->status = $status; return $this; }

    /**
     * Returns the provider response body or error message.
     *
     * @return string|null Log text, or null.
     */
    public function getLog(): ?string { return $this->log; }

    /**
     * Sets the provider response body or error message.
     *
     * @param  string|null $log Log text, or null.
     * @return static            Fluent self for chaining.
     */
    public function setLog(?string $log): static { $this->log = $log; return $this; }

    /**
     * Returns the triggering user UUID string.
     *
     * @return string|null User UUID, or null for system-triggered runs.
     */
    public function getTriggeredBy(): ?string { return $this->triggeredBy; }

    /**
     * Sets the triggering user UUID string.
     *
     * @param  string|null $triggeredBy User UUID, or null.
     * @return static                    Fluent self for chaining.
     */
    public function setTriggeredBy(?string $triggeredBy): static { $this->triggeredBy = $triggeredBy; return $this; }

    /**
     * Returns the UTC start timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC start timestamp.
     */
    public function getStartedAt(): \DateTimeImmutable { return $this->startedAt; }

    /**
     * Sets the UTC start timestamp.
     *
     * @param  \DateTimeImmutable $startedAt Immutable UTC start timestamp.
     * @return static                         Fluent self for chaining.
     */
    public function setStartedAt(\DateTimeImmutable $startedAt): static { $this->startedAt = $startedAt; return $this; }

    /**
     * Returns the UTC completion timestamp, or null when still running.
     *
     * @return \DateTimeImmutable|null Completion timestamp, or null.
     */
    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }

    /**
     * Sets the UTC completion timestamp.
     *
     * @param  \DateTimeImmutable|null $completedAt Completion timestamp, or null.
     * @return static                                Fluent self for chaining.
     */
    public function setCompletedAt(?\DateTimeImmutable $completedAt): static { $this->completedAt = $completedAt; return $this; }
}
