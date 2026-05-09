<?php

/**
 * @file CommentMapper.php
 *
 * Data mapper translating between the Comment domain entity and the
 * CommentEntity Doctrine persistence model.
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Mapper;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Translates between the pure Comment domain entity and the Doctrine
 * CommentEntity persistence model in both directions.
 */
final class CommentMapper
{
    /**
     * Converts a Doctrine CommentEntity to a pure domain Comment.
     *
     * @param  CommentEntity $entity The hydrated Doctrine persistence model to convert.
     * @return Comment               A domain entity reconstituted from the persisted record.
     */
    public function toDomain(CommentEntity $entity): Comment
    {
        return Comment::reconstitute(
            id:         (string) $entity->getId(),
            collection: $entity->getCollection(),
            item:       $entity->getItem(),
            comment:    $entity->getComment(),
            userId:     $entity->getUserId(),
            createdAt:  $entity->getCreatedAt(),
            updatedAt:  $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain Comment to a Doctrine CommentEntity ready for persistence.
     *
     * @param  Comment       $domain The domain entity to convert.
     * @return CommentEntity          A Doctrine model populated from the domain entity.
     */
    public function toPersistence(Comment $domain): CommentEntity
    {
        return (new CommentEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setCollection($domain->getCollection())
            ->setItem($domain->getItem())
            ->setComment($domain->getComment())
            ->setUserId($domain->getUserId())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
