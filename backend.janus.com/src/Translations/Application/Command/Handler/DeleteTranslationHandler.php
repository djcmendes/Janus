<?php

declare(strict_types=1);

namespace App\Translations\Application\Command\Handler;

use App\Translations\Application\Command\DeleteTranslationCommand;
use App\Translations\Domain\Exception\TranslationNotFoundException;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;

final class DeleteTranslationHandler
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    public function handle(DeleteTranslationCommand $command): void
    {
        $translation = $this->repository->findById($command->id);

        if ($translation === null) {
            throw new TranslationNotFoundException($command->id);
        }

        $this->repository->delete($translation);
    }
}
