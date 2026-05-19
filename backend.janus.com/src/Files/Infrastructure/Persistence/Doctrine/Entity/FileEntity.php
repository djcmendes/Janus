<?php

/**
 * @file FileEntity.php
 *
 * Doctrine ORM persistence model for the `files` table.
 * This class is the sole owner of all database-mapping concerns for file records.
 * Domain logic lives exclusively in File (Domain\Entity).
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Entity;

use App\Files\Domain\Entity\Folder;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping file records to the `files` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain File class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'files')]
class FileEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** @var string Storage driver: 'local' or 's3'. */
    #[ORM\Column(length: 20)]
    private string $storage = 'local';

    /** @var string Name as stored on disk. */
    #[ORM\Column(length: 255)]
    private string $filenameDisk;

    /** @var string Original filename presented on download. */
    #[ORM\Column(length: 255)]
    private string $filenameDownload;

    /** @var string|null Human-readable title. */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    /** @var string MIME type. */
    #[ORM\Column(length: 100)]
    private string $type;

    /** @var int|null File size in bytes. */
    #[ORM\Column(nullable: true)]
    private ?int $filesize = null;

    /** @var int|null Image width in pixels. */
    #[ORM\Column(nullable: true)]
    private ?int $width = null;

    /** @var int|null Image height in pixels. */
    #[ORM\Column(nullable: true)]
    private ?int $height = null;

    /** @var string|null UUID of the uploading user — stored as string, no ORM FK. */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $uploadedBy = null;

    #[ORM\ManyToOne(targetEntity: Folder::class)]
    #[ORM\JoinColumn(name: 'folder_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Folder $folder = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUID primary key of this record.
     *
     * @return Uuid|null Doctrine-managed UUID value object, or null before first persist.
     */
    public function getId(): ?Uuid { return $this->id; }

    /**
     * @param Uuid $id The UUID to assign as the primary key.
     * @return static
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * @return string Storage driver ('local' or 's3').
     */
    public function getStorage(): string { return $this->storage; }

    /**
     * @param string $storage Storage driver.
     * @return static
     */
    public function setStorage(string $storage): static { $this->storage = $storage; return $this; }

    /** @return string Disk filename. */
    public function getFilenameDisk(): string { return $this->filenameDisk; }

    /**
     * @param string $filenameDisk Disk filename.
     * @return static
     */
    public function setFilenameDisk(string $filenameDisk): static { $this->filenameDisk = $filenameDisk; return $this; }

    /** @return string Download filename. */
    public function getFilenameDownload(): string { return $this->filenameDownload; }

    /**
     * @param string $filenameDownload Download filename.
     * @return static
     */
    public function setFilenameDownload(string $filenameDownload): static { $this->filenameDownload = $filenameDownload; return $this; }

    /** @return string|null Human-readable title, or null. */
    public function getTitle(): ?string { return $this->title; }

    /**
     * @param string|null $title Title, or null to clear.
     * @return static
     */
    public function setTitle(?string $title): static { $this->title = $title; return $this; }

    /** @return string MIME type. */
    public function getType(): string { return $this->type; }

    /**
     * @param string $type MIME type.
     * @return static
     */
    public function setType(string $type): static { $this->type = $type; return $this; }

    /** @return int|null File size in bytes, or null. */
    public function getFilesize(): ?int { return $this->filesize; }

    /**
     * @param int|null $filesize File size in bytes, or null.
     * @return static
     */
    public function setFilesize(?int $filesize): static { $this->filesize = $filesize; return $this; }

    /** @return int|null Image width in pixels, or null. */
    public function getWidth(): ?int { return $this->width; }

    /**
     * @param int|null $width Image width in pixels, or null.
     * @return static
     */
    public function setWidth(?int $width): static { $this->width = $width; return $this; }

    /** @return int|null Image height in pixels, or null. */
    public function getHeight(): ?int { return $this->height; }

    /**
     * @param int|null $height Image height in pixels, or null.
     * @return static
     */
    public function setHeight(?int $height): static { $this->height = $height; return $this; }

    /** @return string|null UUID of the uploading user, or null. */
    public function getUploadedBy(): ?string { return $this->uploadedBy; }

    /**
     * @param string|null $uploadedBy Uploader UUID, or null.
     * @return static
     */
    public function setUploadedBy(?string $uploadedBy): static { $this->uploadedBy = $uploadedBy; return $this; }

    /**
     * Returns the ORM-managed Folder entity for this file's parent folder.
     *
     * @return Folder|null Parent folder entity, or null when at root.
     */
    public function getFolder(): ?Folder { return $this->folder; }

    /**
     * @param Folder|null $folder Parent folder entity, or null to move to root.
     * @return static
     */
    public function setFolder(?Folder $folder): static { $this->folder = $folder; return $this; }

    /** @return \DateTimeImmutable Upload timestamp. */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * @param \DateTimeImmutable $createdAt Upload timestamp.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** @return \DateTimeImmutable|null Timestamp of last mutation, or null. */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * @param \DateTimeImmutable|null $updatedAt Mutation timestamp, or null.
     * @return static
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
