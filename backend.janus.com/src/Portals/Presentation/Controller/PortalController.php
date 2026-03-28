<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\ArchivePortalCommand;
use App\Portals\Application\Command\CreatePortalCommand;
use App\Portals\Application\Command\Handler\ArchivePortalHandler;
use App\Portals\Application\Command\Handler\CreatePortalHandler;
use App\Portals\Application\Command\Handler\SetPortalCssHandler;
use App\Portals\Application\Command\Handler\UpdatePortalSettingsHandler;
use App\Portals\Application\Command\SetPortalCssCommand;
use App\Portals\Application\Command\UpdatePortalSettingsCommand;
use App\Portals\Application\Query\GetPortalByIdQuery;
use App\Portals\Application\Query\GetPortalDashboardMetricsQuery;
use App\Portals\Application\Query\Handler\GetPortalByIdHandler;
use App\Portals\Application\Query\Handler\GetPortalDashboardMetricsHandler;
use App\Portals\Application\Query\Handler\ListPortalsHandler;
use App\Portals\Application\Query\ListPortalsQuery;
use App\Portals\Domain\Exception\PortalAlreadyExistsException;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Presentation\DTO\CreatePortalRequest;
use App\Portals\Presentation\DTO\UpdatePortalRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/portals', name: 'portals_')]
final class PortalController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard                    $guard,
        private readonly ListPortalsHandler              $listHandler,
        private readonly GetPortalByIdHandler            $getByIdHandler,
        private readonly CreatePortalHandler             $createHandler,
        private readonly UpdatePortalSettingsHandler     $updateHandler,
        private readonly ArchivePortalHandler            $archiveHandler,
        private readonly SetPortalCssHandler             $setCssHandler,
        private readonly GetPortalDashboardMetricsHandler $dashboardHandler,
    ) {}
    /** GET /portals */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);
        $result = $this->listHandler->handle(new ListPortalsQuery($limit, $offset));
        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result->data),
            'meta' => ['total_count' => $result->total, 'filter_count' => count($result->data)],
        ]);
    }
    /** GET /portals/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('PORTAL_VIEW', $id);
        try {
            $dto = $this->getByIdHandler->handle(new GetPortalByIdQuery($id));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** POST /portals */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $req = CreatePortalRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dto = $this->createHandler->handle(new CreatePortalCommand(
                name:      $req->name,
                baseRoute: $req->baseRoute,
                status:    $req->status,
                settings:  $req->settings,
            ));
        } catch (PortalAlreadyExistsException $e) {
            return $this->json($this->error($e->getMessage(), 'PORTAL_EXISTS'), Response::HTTP_CONFLICT);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    /** PATCH /portals/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $req = UpdatePortalRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        try {
            $dto = $this->updateHandler->handle(new UpdatePortalSettingsCommand(
                id:        $id,
                name:      $req->name,
                baseRoute: $req->baseRoute,
                status:    $req->status,
                settings:  $req->settings,
            ));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** DELETE /portals/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $this->archiveHandler->handle(new ArchivePortalCommand($id));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    /** GET /portals/{id}/dashboard */
    #[Route('/{id}/dashboard', name: 'dashboard', methods: ['GET'], priority: -1)]
    public function dashboard(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('PORTAL_VIEW', $id);
        try {
            $dto = $this->dashboardHandler->handle(new GetPortalDashboardMetricsQuery($id));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }

    /** PATCH /portals/{id}/css */
    #[Route('/{id}/css', name: 'set_css', methods: ['PATCH'], priority: -1)]
    public function setCss(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->setCssHandler->handle(new SetPortalCssCommand(
                portalId: $id,
                css:      isset($data['css']) ? (string) $data['css'] : null,
            ));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }

    private function notFound(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'NOT_FOUND']]]];
    }
    private function validationError(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'VALIDATION_ERROR']]]];
    }
    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
