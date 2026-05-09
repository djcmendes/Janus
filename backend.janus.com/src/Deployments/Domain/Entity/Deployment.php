<?php

/**
 * @file Deployment.php
 *
 * Pure domain entity representing a single deployment run. Zero framework dependencies.
 *
 * @package App\Deployments\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use Symfony\Component\Uid\Uuid;

/**
 * A deployment run — records the outcome of triggering a DeploymentProvider's build hook.
 *
 * The constructor generates a new UUIDv7 identity; use reconstitute() to reload an
 * existing record from persistence without creating a new ID or resetting timestamps.
 */
final class Deployment
{
    /** @var string UUIDv7 string primary key. */
    private string $id;

    /** @var string UUID of the DeploymentProvider that was triggered. */
    private string $providerId;

    /** @var DeploymentRunStatus Current lifecycle state of this run. */
    private DeploymentRunStatus $status;

    /** @var string|null Response body or error message from the provider. */
    private ?string $log = null;

    /** @var string|null UUID of the user who triggered this run. */
    private ?string $triggeredBy;

    /** @var \DateTimeImmutable UTC timestamp when the run started. */
    private \DateTimeImmutable $startedAt;

    /** @var \DateTimeImmutable|null UTC timestamp when the run completed, or null if still running. */
    private ?\DateTimeImmutable $completedAt = null;

    /**
     * Creates a new Deployment run with a generated UUIDv7 identity and PENDING status.
     *
     * @param string      $providerId  UUID of the DeploymentProvider being triggered.
     * @param string|null $triggeredBy UUID of the user who initiated the run.
     */
    public function __construct(string $providerId, ?string $triggeredBy = null)
    {
        $this->id          = (string) Uuid::v7();
        $this->providerId  = $providerId;
        $this->status      = DeploymentRunStatus::PENDING;
        $this->triggeredBy = $triggeredBy;
        $this->startedAt   = new \DateTimeImmutable();
    }

    /**
     * Reconstitutes an existing Deployment from persisted data without generating a new ID.
     *
     * @param string                   $id          Existing UUIDv7 string.
     * @param string                   $providerId  UUID of the DeploymentProvider.
     * @param DeploymentRunStatus      $status      Current lifecycle state.
     * @param string|null              $log         Provider response or error message.
     * @param string|null              $triggeredBy UUID of the triggering user.
     * @param \DateTimeImmutable       $startedAt   Original start timestamp.
     * @param \DateTimeImmutable|null  $completedAt Completion timestamp, or null.
     *
     * @return self A Deployment instance reflecting the persisted state.
     */
    public static function reconstitute(
        string               $id,
        string               $providerId,
        DeploymentRunStatus  $status,
        ?string              $log,
        ?string              $triggeredBy,
        \DateTimeImmutable   $startedAt,
        ?\DateTimeImmutable  $completedAt,
    ): self {
        $self              = new self($providerId, $triggeredBy);
        $self->id          = $id;
        $self->status      = $status;
        $self->log         = $log;
        $self->startedAt   = $startedAt;
        $self->completedAt = $completedAt;

        return $self;
    }

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * Returns the UUID of the DeploymentProvider that was triggered.
     *
     * @return string Provider UUID string.
     */
    public function getProviderId(): string { return $this->providerId; }

    /**
     * Returns the current lifecycle state of this run.
     *
     * @return DeploymentRunStatus Enum case (PENDING, RUNNING, SUCCESS, FAILURE).
     */
    public function getStatus(): DeploymentRunStatus { return $this->status; }

    /**
     * Sets the lifecycle status.
     *
     * @param  DeploymentRunStatus $status New status.
     * @return static                       Fluent self for chaining.
     */
    public function setStatus(DeploymentRunStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Returns the provider response body or error message.
     *
     * @return string|null Log text, or null if not yet populated.
     */
    public function getLog(): ?string { return $this->log; }

    /**
     * Sets the provider response body or error message.
     *
     * @param  string|null $log Log text, or null to clear.
     * @return static            Fluent self for chaining.
     */
    public function setLog(?string $log): static
    {
        $this->log = $log;
        return $this;
    }

    /**
     * Returns the UUID of the user who triggered this run.
     *
     * @return string|null User UUID string, or null for system-triggered runs.
     */
    public function getTriggeredBy(): ?string { return $this->triggeredBy; }

    /**
     * Returns the UTC timestamp when the run started.
     *
     * @return \DateTimeImmutable Start timestamp.
     */
    public function getStartedAt(): \DateTimeImmutable { return $this->startedAt; }

    /**
     * Returns the UTC timestamp when the run completed, or null if still running.
     *
     * @return \DateTimeImmutable|null Completion timestamp, or null.
     */
    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }

    /**
     * Marks the run as complete with the given final status and an optional log message.
     *
     * @param  DeploymentRunStatus $status Final status (SUCCESS or FAILURE).
     * @param  string|null         $log    Provider response or error message.
     * @return static                       Fluent self for chaining.
     */
    public function complete(DeploymentRunStatus $status, ?string $log = null): static
    {
        $this->status      = $status;
        $this->log         = $log;
        $this->completedAt = new \DateTimeImmutable();

        return $this;
    }
}
