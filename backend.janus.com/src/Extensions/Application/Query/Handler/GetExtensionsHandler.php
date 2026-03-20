<?php

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionsQuery;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

final class GetExtensionsHandler
{
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /** @return array{data: ExtensionDto[], total: int} */
    public function handle(GetExtensionsQuery $query): array
    {
        $extensions = $this->repository->findPaginated($query->limit, $query->offset, $query->type, $query->enabled);
        $total      = $this->repository->countAll($query->type, $query->enabled);

        return [
            'data'  => array_map(ExtensionDto::fromEntity(...), $extensions),
            'total' => $total,
        ];
    }
}
