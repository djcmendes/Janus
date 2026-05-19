<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;

use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Application\Service\RequestGuard;
use App\Portals\Application\Command\CreateMagnetCommand;
use App\Portals\Application\Command\DeleteMagnetCommand;
use App\Portals\Application\Command\Handler\CreateMagnetHandler;
use App\Portals\Application\Command\Handler\DeleteMagnetHandler;
use App\Portals\Application\Command\Handler\PauseMagnetHandler;
use App\Portals\Application\Command\Handler\TriggerMagnetRunHandler;
use App\Portals\Application\Command\Handler\UpdateMagnetSourceHandler;
use App\Portals\Application\Command\PauseMagnetCommand;
use App\Portals\Application\Command\TriggerMagnetRunCommand;
use App\Portals\Application\Command\UpdateMagnetSourceCommand;
use App\Portals\Application\Query\GetMagnetRunHistoryQuery;
use App\Portals\Application\Query\Handler\GetMagnetRunHistoryHandler;
use App\Portals\Application\Query\Handler\ListMagnetsHandler;
use App\Portals\Application\Query\ListMagnetsQuery;
use App\Portals\Domain\Exception\MagnetNotFoundException;
use App\Portals\Domain\Exception\PortalNotFoundException;
use App\Portals\Presentation\DTO\CreateMagnetRequest;
use App\Portals\Presentation\DTO\UpdateMagnetRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/portals/{portalId}/magnets', name: 'portals_magnets_')]
final class MagnetController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard               $guard,
        private readonly ListMagnetsHandler         $listHandler,
        private readonly CreateMagnetHandler        $createHandler,
        private readonly UpdateMagnetSourceHandler  $updateHandler,
        private readonly PauseMagnetHandler         $pauseHandler,
        private readonly DeleteMagnetHandler        $deleteHandler,
        private readonly TriggerMagnetRunHandler    $triggerHandler,
        private readonly GetMagnetRunHistoryHandler $runHistoryHandler,
    ) {}

    /** GET /portals/{portalId}/magnets */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(string $portalId, Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('PORTAL_VIEW', $portalId);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $result = $this->listHandler->handle(new ListMagnetsQuery($portalId, $limit, $offset));

        return $this->json($result->toArray());
    }

    /** POST /portals/{portalId}/magnets */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(string $portalId, Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $req = CreateMagnetRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $dto = $this->createHandler->handle(new CreateMagnetCommand(
                portalId:           $portalId,
                name:               $req->name,
                sourceType:         $req->sourceType,
                targetCollectionId: $req->targetCollectionId,
                schedule:           $req->schedule,
            ));
        } catch (PortalNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /portals/{portalId}/magnets/{id} */
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(string $portalId, string $id, Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $req = UpdateMagnetRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateHandler->handle(new UpdateMagnetSourceCommand(
                magnetId:     $id,
                sourceType:   $req->sourceType,
                sourceConfig: $req->sourceConfig,
                name:         $req->name,
                schedule:     $req->schedule,
            ));
        } catch (MagnetNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /portals/{portalId}/magnets/{id}/pause */
    #[Route('/{id}/pause', name: 'pause', methods: ['POST'])]
    public function pause(string $portalId, string $id): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $this->pauseHandler->handle(new PauseMagnetCommand($id));
        } catch (MagnetNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /portals/{portalId}/magnets/{id}/trigger */
    #[Route('/{id}/trigger', name: 'trigger', methods: ['POST'])]
    public function trigger(string $portalId, string $id): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::CLI);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $dto = $this->triggerHandler->handle(new TriggerMagnetRunCommand($id));
        } catch (MagnetNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_ACCEPTED);
    }

    /** GET /portals/{portalId}/magnets/{id}/runs */
    #[Route('/{id}/runs', name: 'runs', methods: ['GET'])]
    public function runs(string $portalId, string $id, Request $request): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $this->denyAccessUnlessGranted('PORTAL_VIEW', $portalId);

        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        try {
            $result = $this->runHistoryHandler->handle(
                new GetMagnetRunHistoryQuery($id, $limit, $offset)
            );
        } catch (MagnetNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json($result->toArray());
    }

    /** DELETE /portals/{portalId}/magnets/{id} */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $portalId, string $id): JsonResponse
    {
        $this->guard->validateWebserviceRequest(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        try {
            $this->deleteHandler->handle(new DeleteMagnetCommand($id));
        } catch (MagnetNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function notFound(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'NOT_FOUND']]]];
    }

    private function validationError(string $message): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => 'VALIDATION_ERROR']]]];
    }
}
