<?php

declare(strict_types=1);

namespace App\Files\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'files')]
class File
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** Storage driver: 'local' or 's3' */
    #[ORM\Column(length: 20)]
    private string $storage = 'local';

    /** Name as stored on disk (UUID-based, e.g. "01957abc-….jpg") */
    #[ORM\Column(length: 255)]
    private string $filenameDisk;

    /** Original filename presented on download */
    #[ORM\Column(length: 255)]
    private string $filenameDownload;

    /** Human-readable title */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    /** MIME type */
    #[ORM\Column(length: 100)]
    private string $type;

    #[ORM\Column(nullable: true)]
    private ?int $filesize = null;

    /** Image width in pixels */
    #[ORM\Column(nullable: true)]
    private ?int $width = null;

    /** Image height in pixels */
    #[ORM\Column(nullable: true)]
    private ?int $height = null;

    /** UUID of the uploading user — stored as string, no ORM FK */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $uploadedBy = null;

    #[ORM\ManyToOne(targetEntity: Folder::class)]
    #[ORM\JoinColumn(name: 'folder_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Folder $folder = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string  $filenameDisk,
        string  $filenameDownload,
        string  $type,
        ?int    $filesize   = null,
        ?int    $width      = null,
        ?int    $height     = null,
        string  $storage    = 'local',
    ) {
        $this->id               = Uuid::v7();
        $this->filenameDisk     = $filenameDisk;
        $this->filenameDownload = $filenameDownload;
        $this->type             = $type;
        $this->filesize         = $filesize;
        $this->width            = $width;
        $this->height           = $height;
        $this->storage          = $storage;
        $this->createdAt        = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getStorage(): string { return $this->storage; }
    public function getFilenameDisk(): string { return $this->filenameDisk; }

    public function getFilenameDownload(): string { return $this->filenameDownload; }
    public function setFilenameDownload(string $name): static { $this->filenameDownload = $name; return $this->touch(); }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): static { $this->title = $title; return $this->touch(); }

    public function getType(): string { return $this->type; }
    public function getFilesize(): ?int { return $this->filesize; }
    public function getWidth(): ?int { return $this->width; }
    public function getHeight(): ?int { return $this->height; }

    public function getUploadedBy(): ?string { return $this->uploadedBy; }
    public function setUploadedBy(?string $userId): static { $this->uploadedBy = $userId; return $this->touch(); }

    public function getFolder(): ?Folder { return $this->folder; }
    public function setFolder(?Folder $folder): static { $this->folder = $folder; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
