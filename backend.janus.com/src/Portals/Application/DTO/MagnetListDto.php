<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;

final class MagnetListDto
{
    /** @param MagnetDto[] $data */
    public function __construct(
        public readonly array $data,
        public readonly int   $total,
    ) {}

    public function toArray(): array
    {
        return [
            'data' => array_map(fn (MagnetDto $d) => $d->toArray(), $this->data),
            'meta' => [
                'total_count'  => $this->total,
                'filter_count' => count($this->data),
            ],
        ];
    }
}
