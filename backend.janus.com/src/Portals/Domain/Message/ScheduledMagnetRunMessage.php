<?php
declare(strict_types=1);
namespace App\Portals\Domain\Message;

/**
 * Dispatched by the Symfony Scheduler for each active magnet
 * whose schedule cron expression fires.
 */
final class ScheduledMagnetRunMessage
{
    public function __construct(public readonly string $magnetId) {}
}
