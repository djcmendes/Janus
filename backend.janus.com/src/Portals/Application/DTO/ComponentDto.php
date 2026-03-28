<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;
use App\Portals\Domain\Entity\ComponentDefinition;
final class ComponentDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $type,
        public readonly ?string $collectionId,
        public readonly array   $queryConfig,
        public readonly array   $renderConfig,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}
    public static function fromEntity(ComponentDefinition $c): self
    {
        return new self(
            id:           $c->getId(),
            type:         $c->getType()->value,
            collectionId: $c->getCollectionId(),
            queryConfig:  $c->getQueryConfig()->toArray(),
            renderConfig: $c->getRenderConfig()->toArray(),
            createdAt:    $c->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:    $c->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'type'          => $this->type,
            'collection_id' => $this->collectionId,
            'query_config'  => $this->queryConfig,
            'render_config' => $this->renderConfig,
            'created_at'    => $this->createdAt,
            'updated_at'    => $this->updatedAt,
        ];
    }
}
