<?php

declare(strict_types=1);

namespace App\Settings\Application\Command\Handler;

use App\Settings\Application\Command\UpdateSettingsCommand;
use App\Settings\Application\DTO\SettingsDto;
use App\Settings\Domain\Repository\SettingsRepositoryInterface;

final class UpdateSettingsHandler
{
    public function __construct(
        private readonly SettingsRepositoryInterface $repository,
    ) {}

    public function handle(UpdateSettingsCommand $command): SettingsDto
    {
        $settings = $this->repository->getOrCreate();

        if ($command->projectName !== null) {
            $settings->setProjectName($command->projectName);
        }
        if ($command->defaultLanguage !== null) {
            $settings->setDefaultLanguage($command->defaultLanguage);
        }
        if ($command->defaultAppearance !== null) {
            $settings->setDefaultAppearance($command->defaultAppearance);
        }
        if ($command->projectUrl !== UpdateSettingsCommand::UNCHANGED) {
            $settings->setProjectUrl($command->projectUrl);
        }
        if ($command->projectLogo !== UpdateSettingsCommand::UNCHANGED) {
            $settings->setProjectLogo($command->projectLogo);
        }
        if ($command->projectColor !== UpdateSettingsCommand::UNCHANGED) {
            $settings->setProjectColor($command->projectColor);
        }

        $this->repository->save($settings);

        return SettingsDto::fromEntity($settings);
    }
}
