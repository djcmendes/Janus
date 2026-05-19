<?php

/**
 * @file DeploymentMapper.php
 *
 * Bi-directional mapper between the Deployment domain entity and DeploymentEntity (Doctrine ORM).
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Converts between the pure domain Deployment and its Doctrine persistence counterpart.
 *
 * toDomain() uses Deployment::reconstitute() to avoid side-effects (no new UUID, no timestamp reset).
 * toPersistence() builds a fresh DeploymentEntity hydrated from the domain object's current state.
 */
final readonly class DeploymentMapper
{
    /**
     * Converts a Doctrine DeploymentEntity to a domain Deployment.
     *
     * @param  DeploymentEntity $entity The Doctrine-managed persistence record.
     * @return Deployment                The equivalent pure domain entity.
     */
    public function toDomain(DeploymentEntity $entity): Deployment
    {
        return Deployment::reconstitute(
            id:          (string) $entity->getId(),
            providerId:  $entity->getProviderId(),
            status:      $entity->getStatus(),
            log:         $entity->getLog(),
            triggeredBy: $entity->getTriggeredBy(),
            startedAt:   $entity->getStartedAt(),
            completedAt: $entity->getCompletedAt(),
        );
    }

    /**
     * Converts a domain Deployment to a Doctrine DeploymentEntity ready for persistence.
     *
     * @param  Deployment       $domain The domain entity to persist.
     * @return DeploymentEntity          A hydrated Doctrine entity.
     */
    public function toPersistence(Deployment $domain): DeploymentEntity
    {
        return (new DeploymentEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setProviderId($domain->getProviderId())
            ->setStatus($domain->getStatus())
            ->setLog($domain->getLog())
            ->setTriggeredBy($domain->getTriggeredBy())
            ->setStartedAt($domain->getStartedAt())
            ->setCompletedAt($domain->getCompletedAt());
    }
}
