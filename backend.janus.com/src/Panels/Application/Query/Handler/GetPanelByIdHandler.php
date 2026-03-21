<?php

declare(strict_types=1);

namespace App\Panels\Application\Query\Handler;

use App\Panels\Application\DTO\PanelDto;
use App\Panels\Application\Query\GetPanelByIdQuery;
use App\Panels\Domain\Exception\PanelNotFoundException;
use App\Panels\Domain\Repository\PanelRepositoryInterface;

final class GetPanelByIdHandler
{
    public function __construct(private readonly PanelRepositoryInterface $repository) {}

    public function handle(GetPanelByIdQuery $query): PanelDto
    {
        $panel = $this->repository->findById($query->id);

        if ($panel === null) {
            throw new PanelNotFoundException($query->id);
        }

        return PanelDto::fromEntity($panel);
    }
}
