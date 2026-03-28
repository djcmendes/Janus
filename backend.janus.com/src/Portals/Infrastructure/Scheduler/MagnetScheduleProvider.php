<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Scheduler;

use App\Portals\Domain\Message\ScheduledMagnetRunMessage;
use App\Portals\Domain\Repository\MagnetRepositoryInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Dynamically builds the cron schedule from all active magnets
 * that have a non-null schedule field.
 *
 * The schedule is cached so the database is not queried on every tick.
 * Cache is invalidated automatically when a magnet is updated (via
 * cache key reset on the next scheduler boot cycle).
 *
 * Cron expressions stored on Magnet.schedule follow standard 5-field syntax:
 *   "0 * * * *"   — every hour
 *   "*/15 * * * *" — every 15 minutes
 *   "0 2 * * *"   — daily at 02:00
 */
#[AsSchedule('magnets')]
final class MagnetScheduleProvider implements ScheduleProviderInterface
{
    private ?Schedule $schedule = null;

    public function __construct(
        private readonly MagnetRepositoryInterface $magnetRepository,
        private readonly CacheInterface            $cache,
    ) {}

    public function getSchedule(): Schedule
    {
        return $this->schedule ??= $this->buildSchedule();
    }

    private function buildSchedule(): Schedule
    {
        $schedule = new Schedule();
        $schedule->stateful($this->cache);

        $magnets = $this->magnetRepository->findActiveWithSchedule();

        foreach ($magnets as $magnet) {
            $cron = $magnet->getSchedule();
            if ($cron === null || $cron === '') {
                continue;
            }

            $schedule->add(
                RecurringMessage::cron(
                    $cron,
                    new ScheduledMagnetRunMessage($magnet->getId()),
                )
            );
        }

        return $schedule;
    }
}
