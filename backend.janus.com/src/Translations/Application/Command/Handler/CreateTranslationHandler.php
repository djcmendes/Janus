<?php

declare(strict_types=1);

namespace App\Translations\Application\Command\Handler;

use App\Translations\Application\Command\CreateTranslationCommand;
use App\Translations\Application\DTO\TranslationDto;
use App\Translations\Domain\Entity\Translation;
use App\Translations\Domain\Exception\TranslationAlreadyExistsException;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;

final class CreateTranslationHandler
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    public function handle(CreateTranslationCommand $command): TranslationDto
    {
        if ($this->repository->findByLanguageAndKey($command->language, $command->key) !== null) {
            throw new TranslationAlreadyExistsException($command->language, $command->key);
        }

        $translation = new Translation($command->language, $command->key, $command->value);

        $this->repository->save($translation);

        return TranslationDto::fromEntity($translation);
    }
}
