<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use App\Portals\Domain\ValueObject\ModuleConfig;
use App\Portals\Domain\ValueObject\ModuleType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
#[ORM\Entity]
#[ORM\Table(name: 'modules')]
final class Module
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(length: 100)]
    private string $type;
    #[ORM\Column(length: 255)]
    private string $name;
    #[ORM\Column(name: 'config_json', type: 'json', nullable: true)]
    private ?array $configJson = null;
    #[ORM\Column(name: 'portal_id', type: 'string', length: 36, nullable: true)]
    private ?string $portalId = null;
    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    public function __construct(ModuleType $type, string $name, ?string $portalId = null)
    {
        $this->id        = (string) Uuid::v7();
        $this->type      = $type->value;
        $this->name      = $name;
        $this->portalId  = $portalId;
        $this->createdAt = new \DateTimeImmutable();
    }
    public function updateConfig(string $name, ModuleConfig $config): void
    {
        $this->name       = $name;
        $this->configJson = $config->toArray();
        $this->touch();
    }
    public function getId(): string                    { return $this->id; }
    public function getType(): ModuleType              { return ModuleType::from($this->type); }
    public function getName(): string                  { return $this->name; }
    public function getConfig(): ModuleConfig          { return ModuleConfig::fromArray($this->configJson ?? []); }
    public function getPortalId(): ?string             { return $this->portalId; }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
