<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\Handler\PlaceModuleHandler;
use App\Portals\Application\Command\Handler\RemoveModuleHandler;
use App\Portals\Application\Command\Handler\ReorderModulesHandler;
use App\Portals\Application\Command\PlaceModuleCommand;
use App\Portals\Application\Command\RemoveModuleCommand;
use App\Portals\Application\Command\ReorderModulesCommand;
use App\Portals\Domain\Exception\PlacementNotFoundException;
use App\Portals\Domain\Repository\ModulePlacementRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class PlacementController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard                       $guard,
        private readonly PlaceModuleHandler                 $placeHandler,
        private readonly RemoveModuleHandler                $removeHandler,
        private readonly ReorderModulesHandler              $reorderHandler,
        private readonly ModulePlacementRepositoryInterface $placementRepo,
    ) {}
    /** POST /pages/{pageId}/placements */
    #[Route('/pages/{pageId}/placements', name: 'placements_create', methods: ['POST'])]
    public function create(string $pageId, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        if (empty($data['module_id']) || empty($data['position_name'])) {
            return $this->json($this->validationError('module_id and position_name are required.'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $dto = $this->placeHandler->handle(new PlaceModuleCommand(
            pageId:       $pageId,
            positionName: $data['position_name'],
            moduleId:     $data['module_id'],
            sortOrder:    (int) ($data['sort_order'] ?? 0),
        ));
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    /** GET /pages/{pageId}/placements */
    #[Route('/pages/{pageId}/placements', name: 'placements_list', methods: ['GET'])]
    public function list(string $pageId): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $placements = $this->placementRepo->findByPage($pageId);
        return $this->json([
            'data' => array_map(fn ($p) => \App\Portals\Application\DTO\PlacementDto::fromEntity($p)->toArray(), $placements),
        ]);
    }
    /** DELETE /pages/{pageId}/placements/{id} */
    #[Route('/pages/{pageId}/placements/{id}', name: 'placements_delete', methods: ['DELETE'])]
    public function delete(string $pageId, string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $this->removeHandler->handle(new RemoveModuleCommand($id));
        } catch (PlacementNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    /** POST /pages/{pageId}/placements/reorder */
    #[Route('/pages/{pageId}/placements/reorder', name: 'placements_reorder', methods: ['POST'])]
    public function reorder(string $pageId, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $this->reorderHandler->handle(new ReorderModulesCommand($pageId, $data['items'] ?? []));
        } catch (PlacementNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => null]);
    }
    private function notFound(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'NOT_FOUND']]]]; }
    private function validationError(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'VALIDATION_ERROR']]]]; }
}
