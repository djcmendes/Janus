<?php

declare(strict_types=1);

namespace App\Panels\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'panels')]
class Panel
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** The dashboard this panel belongs to */
    #[ORM\Column(type: 'string', length: 36)]
    private string $dashboardId;

    /** Widget type identifier e.g. "metric", "list", "time-series", "label" */
    #[ORM\Column(type: 'string', length: 64)]
    private string $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note;

    /** Widget-specific configuration (JSON) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options;

    /** Grid position: horizontal offset (column) */
    #[ORM\Column(type: 'integer')]
    private int $positionX;

    /** Grid position: vertical offset (row) */
    #[ORM\Column(type: 'integer')]
    private int $positionY;

    /** Grid width in columns */
    #[ORM\Column(type: 'integer')]
    private int $width;

    /** Grid height in rows */
    #[ORM\Column(type: 'integer')]
    private int $height;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string  $dashboardId,
        string  $type,
        ?string $name      = null,
        ?string $note      = null,
        ?array  $options   = null,
        int     $positionX = 0,
        int     $positionY = 0,
        int     $width     = 6,
        int     $height    = 4,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->dashboardId = $dashboardId;
        $this->type        = $type;
        $this->name        = $name;
        $this->note        = $note;
        $this->options     = $options;
        $this->positionX   = $positionX;
        $this->positionY   = $positionY;
        $this->width       = $width;
        $this->height      = $height;
        $this->createdAt   = new \DateTimeImmutable();
        $this->updatedAt   = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getDashboardId(): string { return $this->dashboardId; }
    public function getType(): string { return $this->type; }
    public function getName(): ?string { return $this->name; }
    public function getNote(): ?string { return $this->note; }
    public function getOptions(): ?array { return $this->options; }
    public function getPositionX(): int { return $this->positionX; }
    public function getPositionY(): int { return $this->positionY; }
    public function getWidth(): int { return $this->width; }
    public function getHeight(): int { return $this->height; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setType(string $type): static
    {
        $this->type      = $type;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setName(?string $name): static
    {
        $this->name      = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setNote(?string $note): static
    {
        $this->note      = $note;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setOptions(?array $options): static
    {
        $this->options   = $options;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setPosition(int $x, int $y): static
    {
        $this->positionX = $x;
        $this->positionY = $y;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setSize(int $width, int $height): static
    {
        $this->width     = $width;
        $this->height    = $height;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
