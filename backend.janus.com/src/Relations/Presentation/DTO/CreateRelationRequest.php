<?php

declare(strict_types=1);

namespace App\Relations\Presentation\DTO;

final class CreateRelationRequest
{
    public function __construct(
        public readonly string  $manyCollection,
        public readonly string  $manyField,
        public readonly ?string $oneCollection      = null,
        public readonly ?string $oneField           = null,
        public readonly ?string $junctionCollection = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['many_collection'])) {
            throw new \InvalidArgumentException('many_collection is required.');
        }
        if (empty($data['many_field'])) {
            throw new \InvalidArgumentException('many_field is required.');
        }

        return new self(
            manyCollection:     trim($data['many_collection']),
            manyField:          trim($data['many_field']),
            oneCollection:      isset($data['one_collection'])      ? trim($data['one_collection'])      : null,
            oneField:           isset($data['one_field'])           ? trim($data['one_field'])           : null,
            junctionCollection: isset($data['junction_collection']) ? trim($data['junction_collection']) : null,
        );
    }
}
