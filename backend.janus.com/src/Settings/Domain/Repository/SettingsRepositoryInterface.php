<?php

declare(strict_types=1);

namespace App\Settings\Domain\Repository;

use App\Settings\Domain\Entity\Settings;

interface SettingsRepositoryInterface
{
    /**
     * Returns the single Settings row, creating it with defaults if it does not exist yet.
     */
    public function getOrCreate(): Settings;

    public function save(Settings $settings): void;
}
