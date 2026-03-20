<?php

declare(strict_types=1);

namespace App\Deployments\Domain\Entity;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * A configured deployment target — holds the URL and credentials
 * needed to trigger an external build/deploy process.
 */
#[ORM\Entity]
#[ORM\Table(name: 'deployment_providers')]
class DeploymentProvider
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 20, enumType: DeploymentProviderType::class)]
    private DeploymentProviderType $type;

    /** Webhook / build-hook URL */
    #[ORM\Column(type: 'text')]
    private string $url;

    /** Extra options — e.g. custom HTTP headers, site ID for Netlify/Vercel */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(string $name, DeploymentProviderType $type, string $url)
    {
        $this->id        = Uuid::v7();
        $this->name      = $name;
        $this->type      = $type;
        $this->url       = $url;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this->touch(); }

    public function getType(): DeploymentProviderType { return $this->type; }

    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): static { $this->url = $url; return $this->touch(); }

    public function getOptions(): ?array { return $this->options; }
    public function setOptions(?array $options): static { $this->options = $options; return $this->touch(); }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
