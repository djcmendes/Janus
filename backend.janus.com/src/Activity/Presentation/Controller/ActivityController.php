<?php

declare(strict_types=1);

namespace App\Activity\Presentation\Controller;

use App\Activity\Infrastructure\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity', name: 'activity_')]
final class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {}

    /** GET /activity */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $items = $this->activityRepository->findBy([], ['timestamp' => 'DESC'], $limit, $offset);
        $total = $this->activityRepository->count([]);

        return $this->json([
            'data' => array_map(fn ($a) => $a->toArray(), $items),
            'meta' => ['total_count' => $total],
        ]);
    }

    /** GET /activity/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $activity = $this->activityRepository->find($id);

        if ($activity === null) {
            return $this->json(['errors' => [['message' => 'Activity not found.']]], 404);
        }

        return $this->json(['data' => $activity->toArray()]);
    }
}
