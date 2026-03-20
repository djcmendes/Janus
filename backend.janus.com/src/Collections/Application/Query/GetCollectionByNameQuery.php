<?php

declare(strict_types=1);

namespace App\Collections\Application\Query;

final class GetCollectionByNameQuery
{
    public function __construct(public readonly string $name) {}
}
