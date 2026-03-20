<?php

declare(strict_types=1);

namespace App\Panels\Application\Query;

final class GetPanelByIdQuery
{
    public function __construct(public readonly string $id) {}
}
