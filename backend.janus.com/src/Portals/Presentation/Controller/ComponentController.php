<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\AssignCenterComponentCommand;
use App\Portals\Application\Command\CreateComponentCommand;
use App\Portals\Application\Command\DeleteComponentCommand;
use App\Portals\Application\Command\Handler\AssignCenterComponentHandler;
use App\Portals\Application\Command\Handler\CreateComponentHandler;
use App\Portals\Application\Command\Handler\DeleteComponentHandler;
use App\Portals\Application\Command\Handler\UpdateComponentHandler;
use App\Portals\Application\Command\UpdateComponentCommand;
use App\Portals\Application\Query\GetComponentByIdQuery;
use App\Portals\Application\Query\GetPageWithLayoutQuery;
use App\Portals\Application\Query\Handler\GetComponentByIdHandler;
use App\Portals\Application\Query\Handler\GetPageWithLayoutHandler;
use App\Portals\Application\Query\Handler\ListComponentsHandler;
use App\Portals\Application\Query\ListComponentsQuery;
use App\Portals\Domain\Exception\ComponentNotFoundException;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Presentation\DTO\CreateComponentRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class ComponentController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard               $guard,
        private readonly ListComponentsHandler      $listHandler,
        private readonly GetComponentByIdHandler    $getByIdHandler,
        private readonly CreateComponentHandler     $createHandler,
        private readonly UpdateComponentHandler     $updateHandler,
        private readonly DeleteComponentHandler     $deleteHandler,
        private readonly AssignCenterComponentHandler $assignHandler,
        private readonly GetPageWithLayoutHandler   $layoutHandler,
    ) {}
    #[Route('/components', name: 'components_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $result = $this->listHandler->handle(new ListComponentsQuery(
            limit:  min((int) $request->query->get('limit', 25), 100),
            offset: (int) $request->query->get('offset', 0),
        ));
        return $this->json([
            'data' => array_map(fn ($d) => $d->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }
    #[Route('/components/{id}', name: 'components_get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        try {
            $dto = $this->getByIdHandler->handle(new GetComponentByIdQuery($id));
        } catch (ComponentNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    #[Route('/components', name: 'components_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $req = CreateComponentRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dto = $this->createHandler->handle(new CreateComponentCommand($req->type, $req->collectionId, $req->queryConfig, $req->renderConfig));
        } catch (\ValueError $e) {
            return $this->json($this->validationError('Invalid component type.'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    #[Route('/components/{id}', name: 'components_patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->updateHandler->handle(new UpdateComponentCommand($id, $data['collection_id'] ?? null, $data['query_config'] ?? [], $data['render_config'] ?? []));
        } catch (ComponentNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    #[Route('/components/{id}', name: 'components_delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $this->deleteHandler->handle(new DeleteComponentCommand($id));
        } catch (ComponentNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    /** GET /pages/{id}/layout — full resolved layout */
    #[Route('/pages/{id}/layout', name: 'pages_layout', methods: ['GET'])]
    public function layout(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        try {
            $dto = $this->layoutHandler->handle(new GetPageWithLayoutQuery($id));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** POST /pages/{id}/center-component */
    #[Route('/pages/{id}/center-component', name: 'pages_assign_center', methods: ['POST'])]
    public function assignCenter(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->assignHandler->handle(new AssignCenterComponentCommand($id, $data['component_id'] ?? null));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    private function notFound(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'NOT_FOUND']]]]; }
    private function validationError(string $m): array { return ['errors' => [['message' => $m, 'extensions' => ['code' => 'VALIDATION_ERROR']]]]; }
}
