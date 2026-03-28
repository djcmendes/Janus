<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;

final class PortalDashboardMetricsDto
{
    public function __construct(
        public readonly string  $portalId,
        public readonly int     $totalPages,
        public readonly int     $publishedPages,
        public readonly int     $draftPages,
        public readonly int     $activeMagnets,
        public readonly ?string $lastMagnetRun,
        /** @var array<int, array<string, mixed>> */
        public readonly array   $recentActivity,
    ) {}

    public function toArray(): array
    {
        return [
            'portal_id'       => $this->portalId,
            'total_pages'     => $this->totalPages,
            'published_pages' => $this->publishedPages,
            'draft_pages'     => $this->draftPages,
            'active_magnets'  => $this->activeMagnets,
            'last_magnet_run' => $this->lastMagnetRun,
            'recent_activity' => $this->recentActivity,
        ];
    }
}
