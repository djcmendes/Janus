<?php

/**
 * @file GetVersionByIdHandler.php
 *
 * Query handler that retrieves a single Version by UUID and returns it as a DTO.
 *
 * @package App\Versions\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionByIdQuery;
use App\Versions\Domain\Exception\VersionNotFoundException;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

/**
 * Handles GetVersionByIdQuery by looking up the Version in the repository
 * and converting it to a VersionDto for the presentation layer.
 */
final class GetVersionByIdHandler
{
    /**
     * @param VersionRepositoryInterface $repository Storage and retrieval of Version records.
     */
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /**
     * Returns a VersionDto for the record identified by query id.
     *
     * @param  GetVersionByIdQuery $query Payload carrying the UUID to look up.
     * @return VersionDto                 DTO of the found Version.
     *
     * @throws VersionNotFoundException When no Version exists for the given id.
     */
    public function handle(GetVersionByIdQuery $query): VersionDto
    {
        $version = $this->repository->findById($query->id);

        if ($version === null) {
            throw new VersionNotFoundException($query->id);
        }

        return VersionDto::fromEntity($version);
    }
}
