<?php

declare(strict_types=1);

namespace App\Flows\Domain\Enum;

enum TriggerType: string
{
    /** Fired manually via POST /flows/:id/trigger */
    case MANUAL    = 'manual';
    /** Fired on item create/update/delete events */
    case ACTION    = 'action';
    /** Fired on a cron schedule */
    case SCHEDULE  = 'schedule';
    /** Fired on incoming webhook */
    case WEBHOOK   = 'webhook';
}
