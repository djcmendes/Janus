<?php

declare(strict_types=1);

namespace App\Comments\Application\Query;

final class GetCommentByIdQuery
{
    public function __construct(public readonly string $id) {}
}
