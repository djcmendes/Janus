<?php

/**
 * @file GetFieldsHandler.php
 *
 * CQRS query handler for retrieving a paginated list of all field metadata records.
 *
 * @package App\Fields\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsQuery;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles GetFieldsQuery — returns a paginated FieldDto array and total count.
 */
final class GetFieldsHandler
{
    /**
     * @param FieldMetaRepositoryInterface $repository Field persistence store.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /**
     * Retrieves a paginated list of all field metadata records.
     *
     * @param  GetFieldsQuery $query Pagination parameters.
     * @return array{data: FieldDto[], total: int} Paginated DTOs and total count.
     */
    public function handle(GetFieldsQuery $query): array
    {
        $fields = $this->repository->findPaginated($query->limit, $query->offset);
        $total  = $this->repository->countAll();

        return [
            'data'  => array_map(FieldDto::fromEntity(...), $fields),
            'total' => $total,
        ];
    }
}
