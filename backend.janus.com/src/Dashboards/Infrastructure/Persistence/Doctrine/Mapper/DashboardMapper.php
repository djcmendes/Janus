<?php

/**
 * @file DashboardMapper.php
 *
 * Bi-directional mapper between the Dashboard domain entity and DashboardEntity (Doctrine ORM).
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Mapper;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;

/**
 * Converts between the pure domain Dashboard and its Doctrine persistence counterpart.
 *
 * toDomain() uses Dashboard::reconstitute() to avoid side-effects (no new UUID, no timestamp reset).
 * toPersistence() builds a fresh DashboardEntity hydrated from the domain object's current state.
 */
final class DashboardMapper
{
    /**
     * Converts a Doctrine DashboardEntity to a domain Dashboard.
     *
     * @param  DashboardEntity $entity The Doctrine-managed persistence record.
     * @return Dashboard                The equivalent pure domain entity.
     */
    public function toDomain(DashboardEntity $entity): Dashboard
    {
        return Dashboard::reconstitute(
            id:        $entity->getId(),
            name:      $entity->getName(),
            icon:      $entity->getIcon(),
            note:      $entity->getNote(),
            userId:    $entity->getUserId(),
            createdAt: $entity->getCreatedAt(),
            updatedAt: $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain Dashboard to a Doctrine DashboardEntity ready for persistence.
     *
     * @param  Dashboard       $domain The domain entity to persist.
     * @return DashboardEntity          A hydrated Doctrine entity.
     */
    public function toPersistence(Dashboard $domain): DashboardEntity
    {
        return (new DashboardEntity())
            ->setId($domain->getId())
            ->setName($domain->getName())
            ->setIcon($domain->getIcon())
            ->setNote($domain->getNote())
            ->setUserId($domain->getUserId())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
