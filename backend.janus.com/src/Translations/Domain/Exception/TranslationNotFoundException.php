<?php

declare(strict_types=1);

namespace App\Translations\Domain\Exception;

final class TranslationNotFoundException extends \RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Translation '{$id}' not found.");
    }
}
