<?php

/**
 * @file FileMapper.php
 *
 * Data mapper translating between the File domain entity and the
 * FileEntity Doctrine persistence model.
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Mapper
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Mapper;

use App\Files\Domain\Entity\File;
use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Translates between the pure File domain entity and the Doctrine
 * FileEntity persistence model in both directions.
 *
 * Note: the Folder ORM relationship on FileEntity is intentionally not set
 * by toPersistence(). The FileRepository resolves and assigns the Folder
 * entity via the entity manager after calling this mapper, keeping the
 * mapper free of infrastructure dependencies.
 */
final readonly class FileMapper
{
    /**
     * Converts a Doctrine FileEntity to a pure domain File.
     *
     * @param  FileEntity $entity The hydrated Doctrine persistence model to convert.
     * @return File                A domain entity reconstituted from the persisted record.
     */
    public function toDomain(FileEntity $entity): File
    {
        return File::reconstitute(
            id:               (string) $entity->getId(),
            storage:          $entity->getStorage(),
            filenameDisk:     $entity->getFilenameDisk(),
            filenameDownload: $entity->getFilenameDownload(),
            title:            $entity->getTitle(),
            type:             $entity->getType(),
            filesize:         $entity->getFilesize(),
            width:            $entity->getWidth(),
            height:           $entity->getHeight(),
            uploadedBy:       $entity->getUploadedBy(),
            folderId:         $entity->getFolder() !== null ? (string) $entity->getFolder()->getId() : null,
            createdAt:        $entity->getCreatedAt(),
            updatedAt:        $entity->getUpdatedAt(),
        );
    }

    /**
     * Converts a domain File to a Doctrine FileEntity ready for persistence.
     *
     * The Folder ORM relationship is left null here; the FileRepository sets it
     * separately via an entity manager lookup.
     *
     * @param  File       $domain The domain entity to convert.
     * @return FileEntity          A Doctrine model populated from the domain entity.
     */
    public function toPersistence(File $domain): FileEntity
    {
        return (new FileEntity())
            ->setId(Uuid::fromString($domain->getId()))
            ->setStorage($domain->getStorage())
            ->setFilenameDisk($domain->getFilenameDisk())
            ->setFilenameDownload($domain->getFilenameDownload())
            ->setTitle($domain->getTitle())
            ->setType($domain->getType())
            ->setFilesize($domain->getFilesize())
            ->setWidth($domain->getWidth())
            ->setHeight($domain->getHeight())
            ->setUploadedBy($domain->getUploadedBy())
            ->setCreatedAt($domain->getCreatedAt())
            ->setUpdatedAt($domain->getUpdatedAt());
    }
}
