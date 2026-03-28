<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;

use App\Portals\Application\DTO\MagnetRunDto;
use App\Portals\Application\DTO\MagnetRunListDto;
use App\Portals\Application\Query\GetMagnetRunHistoryQuery;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use App\Portals\Domain\Repository\MagnetRunRepositoryInterface;

final class GetMagnetRunHistoryHandler
{
    public function __construct(
        private readonly MagnetRepositoryInterface    $magnetRepository,
        private readonly MagnetRunRepositoryInterface $runRepository,
    ) {}

    public function handle(GetMagnetRunHistoryQuery $query): MagnetRunListDto
    {
        $magnet = $this->magnetRepository->findById($query->magnetId);
        if ($magnet === null) {
            throw new MagnetNotFoundException($query->magnetId);
        }

        $runs = $this->runRepository->findByMagnetId(
            $query->magnetId,
            $query->limit,
            $query->offset,
        );

        return new MagnetRunListDto(
            data:  array_map(fn ($r) => MagnetRunDto::fromEntity($r), $runs),
            total: $this->runRepository->countByMagnetId($query->magnetId),
        );
    }
}
