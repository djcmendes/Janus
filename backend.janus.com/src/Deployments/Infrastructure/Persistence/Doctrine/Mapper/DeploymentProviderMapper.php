<?php

/**
 * @file DeploymentProviderMapper.php
 *
 * Bi-directional mapper between the DeploymentProvider domain entity and
 * DeploymentProviderEntity (Doctrine ORM).
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Converts between the pure domain DeploymentProvider and its Doctrine persistence counterpart.
 *
 * toDomain() uses DeploymentProvider::reconstitute() to avoid side-effects.
 * toPersistence() builds a fresh DeploymentProviderEntity hydrated from the domain object's state.
 */
final readonly class DeploymentProviderMapper
{
    /**
     * Converts a Doctrine DeploymentProviderEntity to a domain DeploymentProvider.
     *
     * @param  DeploymentProviderEntity $entity The Doctrine-managed persistence record.
     * @return DeploymentProvider                The equivalent pure domain entity.
     */
    public function toDomain(DeploymentProviderEntity $entity): DeploymentProvider
    {
        return DeploymentProvider::reconstitute(
            id:        (string) $entity->getId(),
            name:      $entity->getName(),
            type:      $entity->getType(),
            url:       $entity->getUrl(),
            options:   $entity->getOptions(),
            isActive:  $entity->isActive(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain DeploymentProvider to a Doctrine DeploymentProviderEntity ready for persistence.
     *
     * @param  DeploymentProvider       $domain The domain entity to persist.
     * @return DeploymentProviderEntity          A hydrated Doctrine entity.
     */
    public function toPersistence(DeploymentProvider $domain): DeploymentProviderEntity
    {
        return (new DeploymentProviderEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setName($domain->getName())
            ->setType($domain->getType())
            ->setUrl($domain->getUrl())
            ->setOptions($domain->getOptions())
            ->setIsActive($domain->isActive())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
