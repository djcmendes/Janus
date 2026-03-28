<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\CreateLayoutTemplateCommand;
use App\Portals\Application\Command\Handler\CreateLayoutTemplateHandler;
use App\Portals\Application\Command\Handler\UpdateLayoutTemplateHandler;
use App\Portals\Application\Command\UpdateLayoutTemplateCommand;
use App\Portals\Application\Query\GetLayoutTemplateByIdQuery;
use App\Portals\Application\Query\Handler\GetLayoutTemplateByIdHandler;
use App\Portals\Application\Query\Handler\ListLayoutTemplatesHandler;
use App\Portals\Application\Query\ListLayoutTemplatesQuery;
use App\Portals\Domain\Exception\LayoutTemplateNotFoundException;
use App\Portals\Presentation\DTO\CreateLayoutTemplateRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/layout-templates', name: 'layout_templates_')]
final class LayoutTemplateController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard                 $guard,
        private readonly ListLayoutTemplatesHandler   $listHandler,
        private readonly GetLayoutTemplateByIdHandler $getByIdHandler,
        private readonly CreateLayoutTemplateHandler  $createHandler,
        private readonly UpdateLayoutTemplateHandler  $updateHandler,
    ) {}
    /** GET /layout-templates */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);
        $result = $this->listHandler->handle(new ListLayoutTemplatesQuery($limit, $offset));
        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }
    /** GET /layout-templates/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        try {
            $dto = $this->getByIdHandler->handle(new GetLayoutTemplateByIdQuery($id));
        } catch (LayoutTemplateNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** POST /layout-templates */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $req = CreateLayoutTemplateRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dto = $this->createHandler->handle(new CreateLayoutTemplateCommand(
                name:           $req->name,
                positions:      $req->positions,
                templateMarkup: $req->templateMarkup,
            ));
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    /** PATCH /layout-templates/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->updateHandler->handle(new UpdateLayoutTemplateCommand(
                id:             $id,
                name:           $data['name']            ?? '',
                positions:      $data['positions']       ?? [],
                templateMarkup: $data['template_markup'] ?? '',
            ));
        } catch (LayoutTemplateNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
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
}
