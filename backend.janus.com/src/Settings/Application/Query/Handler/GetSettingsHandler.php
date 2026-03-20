<?php

declare(strict_types=1);

namespace App\Settings\Application\Query\Handler;

use App\Settings\Application\DTO\SettingsDto;
use App\Settings\Application\Query\GetSettingsQuery;
use App\Settings\Domain\Repository\SettingsRepositoryInterface;

final class GetSettingsHandler
{
    public function __construct(
        private readonly SettingsRepositoryInterface $repository,
    ) {}

    public function handle(GetSettingsQuery $query): SettingsDto
    {
        return SettingsDto::fromEntity($this->repository->getOrCreate());
    }
}
