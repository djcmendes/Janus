<?php

declare(strict_types=1);

namespace App\Translations\Domain\Exception;

final class TranslationAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $language, string $key)
    {
        parent::__construct("A translation for key '{$key}' in language '{$language}' already exists.");
    }
}
