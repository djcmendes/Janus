<?php

declare(strict_types=1);

namespace App\Translations\Application\Query;

final class GetTranslationByIdQuery
{
    public function __construct(public readonly string $id) {}
}
