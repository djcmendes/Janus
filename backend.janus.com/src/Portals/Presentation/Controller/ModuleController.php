<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\CreateModuleCommand;
use App\Portals\Application\Command\DeleteModuleCommand;
use App\Portals\Application\Command\Handler\CreateModuleHandler;
use App\Portals\Application\Command\Handler\DeleteModuleHandler;
use App\Portals\Application\Command\Handler\UpdateModuleConfigHandler;
use App\Portals\Application\Command\UpdateModuleConfigCommand;
use App\Portals\Application\Query\GetModuleByIdQuery;
use App\Portals\Application\Query\Handler\GetModuleByIdHandler;
use App\Portals\Application\Query\Handler\ListModulesHandler;
use App\Portals\Application\Query\ListModulesQuery;
use App\Portals\Domain\Exception\ModuleNotFoundException;
use App\Portals\Presentation\DTO\CreateModuleRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/modules', name: 'modules_')]
final class ModuleController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard              $guard,
        private readonly ListModulesHandler        $listHandler,
        private readonly GetModuleByIdHandler      $getByIdHandler,
        private readonly CreateModuleHandler       $createHandler,
        private readonly UpdateModuleConfigHandler $updateHandler,
        private readonly DeleteModuleHandler       $deleteHandler,
    ) {}
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $result = $this->listHandler->handle(new ListModulesQuery(
            limit:    min((int) $request->query->get('limit', 25), 100),
            offset:   (int) $request->query->get('offset', 0),
            portalId: $request->query->get('portal_id'),
        ));
        return $this->json([
            'data' => array_map(fn ($d) => $d->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        try {
            $dto = $this->getByIdHandler->handle(new GetModuleByIdQuery($id));
        } catch (ModuleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $req = CreateModuleRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dto = $this->createHandler->handle(new CreateModuleCommand($req->type, $req->name, $req->config, $req->portalId));
        } catch (\ValueError $e) {
            return $this->json($this->validationError('Invalid module type.'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->updateHandler->handle(new UpdateModuleConfigCommand(
                id:     $id,
                name:   $data['name']   ?? '',
                config: $data['config'] ?? [],
            ));
        } catch (ModuleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $this->deleteHandler->handle(new DeleteModuleCommand($id));
        } catch (ModuleNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    private function notFound(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'NOT_FOUND']]]]; }
    private function validationError(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'VALIDATION_ERROR']]]]; }
}
