<?php

/**
 * @file File.php
 *
 * Pure domain entity representing an uploaded file record.
 * Contains no framework or persistence dependencies.
 *
 * @package App\Files\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * File record capturing the stored file's metadata, location, and ownership.
 *
 * A UUIDv7 string identifier is generated on construction. All Doctrine
 * mapping concerns live exclusively in FileEntity (Infrastructure layer).
 */
final class File
{
    /** @var string UUID string of the persisted record. */
    private string $id;

    /** @var string Storage driver: 'local' or 's3'. */
    private string $storage;

    /** @var string Name as stored on disk (UUID-based, e.g. "01957abc-….jpg"). */
    private string $filenameDisk;

    /** @var string Original filename presented on download. */
    private string $filenameDownload;

    /** @var string|null Human-readable title. */
    private ?string $title = null;

    /** @var string MIME type. */
    private string $type;

    /** @var int|null File size in bytes. */
    private ?int $filesize;

    /** @var int|null Image width in pixels. */
    private ?int $width;

    /** @var int|null Image height in pixels. */
    private ?int $height;

    /** @var string|null UUID of the uploading user — stored as string, no FK. */
    private ?string $uploadedBy = null;

    /** @var string|null UUID of the parent folder, or null when at root. */
    private ?string $folderId = null;

    /** @var DateTimeImmutable Upload timestamp. */
    private DateTimeImmutable $createdAt;

    /** @var DateTimeImmutable|null Timestamp of the last mutation. */
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @param string   $filenameDisk     Name used on disk.
     * @param string   $filenameDownload Original client filename.
     * @param string   $type             MIME type.
     * @param int|null $filesize         Size in bytes, or null.
     * @param int|null $width            Image width in pixels, or null.
     * @param int|null $height           Image height in pixels, or null.
     * @param string   $storage          Storage driver (default: 'local').
     */
    public function __construct(
        string $filenameDisk,
        string $filenameDownload,
        string $type,
        ?int   $filesize = null,
        ?int   $width    = null,
        ?int   $height   = null,
        string $storage  = 'local',
    ) {
        $this->id               = (string) Uuid::v7();
        $this->filenameDisk     = $filenameDisk;
        $this->filenameDownload = $filenameDownload;
        $this->type             = $type;
        $this->filesize         = $filesize;
        $this->width            = $width;
        $this->height           = $height;
        $this->storage          = $storage;
        $this->createdAt        = new DateTimeImmutable();
    }

    /**
     * Reconstructs a File from persisted data, bypassing the auto-generated
     * id and createdAt set by the constructor.
     *
     * Used exclusively by FileMapper when converting from FileEntity.
     *
     * @param string                $id               UUID string of the persisted record.
     * @param string                $storage          Storage driver.
     * @param string                $filenameDisk     Disk filename.
     * @param string                $filenameDownload Download filename.
     * @param string|null           $title            Human-readable title, or null.
     * @param string                $type             MIME type.
     * @param int|null              $filesize         File size in bytes, or null.
     * @param int|null              $width            Image width in pixels, or null.
     * @param int|null              $height           Image height in pixels, or null.
     * @param string|null           $uploadedBy       Uploader UUID, or null.
     * @param string|null           $folderId         Parent folder UUID, or null.
     * @param DateTimeImmutable     $createdAt        Original upload timestamp.
     * @param DateTimeImmutable|null $updatedAt       Timestamp of last mutation, or null.
     *
     * @return self A fully-populated File with all persisted values.
     */
    public static function reconstitute(
        string            $id,
        string            $storage,
        string            $filenameDisk,
        string            $filenameDownload,
        ?string           $title,
        string            $type,
        ?int              $filesize,
        ?int              $width,
        ?int              $height,
        ?string           $uploadedBy,
        ?string           $folderId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        $instance = new self($filenameDisk, $filenameDownload, $type, $filesize, $width, $height, $storage);

        $instance->id        = $id;
        $instance->title     = $title;
        $instance->uploadedBy = $uploadedBy;
        $instance->folderId  = $folderId;
        $instance->createdAt = $createdAt;
        $instance->updatedAt = $updatedAt;

        return $instance;
    }

    /** @return string UUID of this file record. */
    public function getId(): string { return $this->id; }

    /** @return string Storage driver ('local' or 's3'). */
    public function getStorage(): string { return $this->storage; }

    /** @return string Disk filename. */
    public function getFilenameDisk(): string { return $this->filenameDisk; }

    /** @return string Download filename. */
    public function getFilenameDownload(): string { return $this->filenameDownload; }

    /**
     * @param string $name New download filename.
     * @return static
     */
    public function setFilenameDownload(string $name): static { $this->filenameDownload = $name; return $this->touch(); }

    /** @return string|null Human-readable title, or null. */
    public function getTitle(): ?string { return $this->title; }

    /**
     * @param string|null $title Title, or null to clear.
     * @return static
     */
    public function setTitle(?string $title): static { $this->title = $title; return $this->touch(); }

    /** @return string MIME type. */
    public function getType(): string { return $this->type; }

    /** @return int|null File size in bytes, or null. */
    public function getFilesize(): ?int { return $this->filesize; }

    /** @return int|null Image width in pixels, or null. */
    public function getWidth(): ?int { return $this->width; }

    /** @return int|null Image height in pixels, or null. */
    public function getHeight(): ?int { return $this->height; }

    /** @return string|null UUID of the uploading user, or null. */
    public function getUploadedBy(): ?string { return $this->uploadedBy; }

    /**
     * @param string|null $userId Uploader UUID, or null.
     * @return static
     */
    public function setUploadedBy(?string $userId): static { $this->uploadedBy = $userId; return $this->touch(); }

    /** @return string|null UUID of the parent folder, or null when at root. */
    public function getFolderId(): ?string { return $this->folderId; }

    /**
     * @param string|null $folderId Parent folder UUID, or null to move to root.
     * @return static
     */
    public function setFolderId(?string $folderId): static { $this->folderId = $folderId; return $this->touch(); }

    /** @return DateTimeImmutable Upload timestamp. */
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    /** @return DateTimeImmutable|null Timestamp of last mutation, or null. */
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
