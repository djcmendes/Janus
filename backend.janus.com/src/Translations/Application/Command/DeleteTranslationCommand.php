<?php

declare(strict_types=1);

namespace App\Translations\Application\Command;

final class DeleteTranslationCommand
{
    public function __construct(public readonly string $id) {}
}
