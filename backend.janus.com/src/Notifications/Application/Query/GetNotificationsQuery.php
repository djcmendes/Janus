<?php

declare(strict_types=1);

namespace App\Notifications\Application\Query;

final class GetNotificationsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $recipientId = null,
        public readonly ?bool   $read        = null,
    ) {}
}
