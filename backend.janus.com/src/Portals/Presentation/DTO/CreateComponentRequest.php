<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class CreateComponentRequest
{
    public function __construct(
        public readonly string  $type,
        public readonly ?string $collectionId = null,
        public readonly array   $queryConfig  = [],
        public readonly array   $renderConfig = [],
    ) {}
    public static function fromArray(array $data): self
    {
        if (empty($data['type'])) { throw new \InvalidArgumentException('type is required.'); }
        return new self(
            type:         $data['type'],
            collectionId: $data['collection_id'] ?? null,
            queryConfig:  $data['query_config']  ?? [],
            renderConfig: $data['render_config']  ?? [],
        );
    }
}
