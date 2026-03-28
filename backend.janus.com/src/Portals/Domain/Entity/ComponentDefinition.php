<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use App\Portals\Domain\ValueObject\ComponentType;
use App\Portals\Domain\ValueObject\QueryConfig;
use App\Portals\Domain\ValueObject\RenderConfig;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
#[ORM\Entity]
#[ORM\Table(name: 'components')]
final class ComponentDefinition
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(length: 100)]
    private string $type;
    #[ORM\Column(name: 'collection_id', length: 255, nullable: true)]
    private ?string $collectionId = null;
    #[ORM\Column(name: 'query_config_json', type: 'json', nullable: true)]
    private ?array $queryConfigJson = null;
    #[ORM\Column(name: 'render_config_json', type: 'json', nullable: true)]
    private ?array $renderConfigJson = null;
    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    public function __construct(ComponentType $type, ?string $collectionId = null)
    {
        $this->id           = (string) Uuid::v7();
        $this->type         = $type->value;
        $this->collectionId = $collectionId;
        $this->createdAt    = new \DateTimeImmutable();
    }
    public function update(?string $collectionId, QueryConfig $queryConfig, RenderConfig $renderConfig): void
    {
        $this->collectionId     = $collectionId;
        $this->queryConfigJson  = $queryConfig->toArray();
        $this->renderConfigJson = $renderConfig->toArray();
        $this->touch();
    }
    public function getId(): string                    { return $this->id; }
    public function getType(): ComponentType           { return ComponentType::from($this->type); }
    public function getCollectionId(): ?string         { return $this->collectionId; }
    public function getQueryConfig(): QueryConfig      { return QueryConfig::fromArray($this->queryConfigJson ?? []); }
    public function getRenderConfig(): RenderConfig    { return RenderConfig::fromArray($this->renderConfigJson ?? []); }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
