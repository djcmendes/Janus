<?php
declare(strict_types=1);
namespace App\Portals\Application\Query\Handler;

use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use App\Portals\Application\DTO\PortalDashboardMetricsDto;
use App\Portals\Application\Query\GetPortalDashboardMetricsQuery;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Domain\Repository\PageRepositoryInterface;
use App\Portals\Domain\Repository\PortalRepositoryInterface;
use App\Portals\Domain\ValueObject\PageStatus;

final class GetPortalDashboardMetricsHandler
{
    public function __construct(
        private readonly PortalRepositoryInterface   $portalRepository,
        private readonly PageRepositoryInterface     $pageRepository,
        private readonly ActivityRepositoryInterface $activityRepository,
    ) {}

    public function handle(GetPortalDashboardMetricsQuery $query): PortalDashboardMetricsDto
    {
        $portal = $this->portalRepository->findById($query->portalId);
        if ($portal === null) {
            throw new PortalNotFoundException($query->portalId);
        }

        $totalPages     = $this->pageRepository->countByPortal($query->portalId);
        $publishedPages = $this->pageRepository->countByPortalAndStatus($query->portalId, PageStatus::PUBLISHED->value);
        $draftPages     = $this->pageRepository->countByPortalAndStatus($query->portalId, PageStatus::DRAFT->value);

        $recentActivity = array_map(
            static fn ($a) => [
                'id'         => $a->getId(),
                'action'     => $a->getAction(),
                'collection' => $a->getCollection(),
                'item'       => $a->getItem(),
                'timestamp'  => $a->getTimestamp()->format(\DateTimeInterface::ATOM),
            ],
            $this->activityRepository->findPaginated(
                limit:      $query->activityLimit,
                offset:     0,
                collection: 'portals',
            )
        );

        return new PortalDashboardMetricsDto(
            portalId:       $query->portalId,
            totalPages:     $totalPages,
            publishedPages: $publishedPages,
            draftPages:     $draftPages,
            activeMagnets:  0,      // Phase 5 — Magnets not yet implemented
            lastMagnetRun:  null,   // Phase 5 — Magnets not yet implemented
            recentActivity: $recentActivity,
        );
    }
}
