<?php

declare(strict_types=1);

namespace App\Translations\Application\Command\Handler;

use App\Translations\Application\Command\UpdateTranslationCommand;
use App\Translations\Application\DTO\TranslationDto;
use App\Translations\Domain\Exception\TranslationNotFoundException;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;

final class UpdateTranslationHandler
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    public function handle(UpdateTranslationCommand $command): TranslationDto
    {
        $translation = $this->repository->findById($command->id);

        if ($translation === null) {
            throw new TranslationNotFoundException($command->id);
        }

        $translation->setValue($command->value);

        $this->repository->save($translation);

        return TranslationDto::fromEntity($translation);
    }
}
