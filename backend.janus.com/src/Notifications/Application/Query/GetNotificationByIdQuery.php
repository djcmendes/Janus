<?php

declare(strict_types=1);

namespace App\Notifications\Application\Query;

final class GetNotificationByIdQuery
{
    public function __construct(public readonly string $id) {}
}
