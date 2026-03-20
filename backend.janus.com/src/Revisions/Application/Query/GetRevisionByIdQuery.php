<?php

declare(strict_types=1);

namespace App\Revisions\Application\Query;

final class GetRevisionByIdQuery
{
    public function __construct(public readonly string $id) {}
}
