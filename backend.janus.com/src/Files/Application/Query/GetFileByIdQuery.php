<?php

declare(strict_types=1);

namespace App\Files\Application\Query;

final class GetFileByIdQuery
{
    public function __construct(public readonly string $id) {}
}
