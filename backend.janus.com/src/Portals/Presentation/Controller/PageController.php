<?php
declare(strict_types=1);
namespace App\Portals\Presentation\Controller;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use App\Portals\Application\Command\CreatePageCommand;
use App\Portals\Application\Command\Handler\CreatePageHandler;
use App\Portals\Application\Command\Handler\MovePageHandler;
use App\Portals\Application\Command\Handler\PublishPageHandler;
use App\Portals\Application\Command\Handler\SetPageAclHandler;
use App\Portals\Application\Command\Handler\SetPageCustomCssHandler;
use App\Portals\Application\Command\Handler\UnpublishPageHandler;
use App\Portals\Application\Command\MovePageCommand;
use App\Portals\Application\Command\PublishPageCommand;
use App\Portals\Application\Command\SetPageAclCommand;
use App\Portals\Application\Command\SetPageCustomCssCommand;
use App\Portals\Application\Command\UnpublishPageCommand;
use App\Portals\Application\Query\GetPageTreeQuery;
use App\Portals\Application\Query\Handler\GetPageTreeHandler;
use App\Portals\Domain\Exception\PageNotFoundException;
use App\Portals\Presentation\DTO\CreatePageRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
final class PageController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard           $guard,
        private readonly GetPageTreeHandler     $treeHandler,
        private readonly CreatePageHandler      $createHandler,
        private readonly MovePageHandler        $moveHandler,
        private readonly PublishPageHandler     $publishHandler,
        private readonly UnpublishPageHandler   $unpublishHandler,
        private readonly SetPageCustomCssHandler $setCssHandler,
        private readonly SetPageAclHandler      $setAclHandler,
    ) {}
    /** GET /portals/{portalId}/pages */
    #[Route('/portals/{portalId}/pages', name: 'pages_tree', methods: ['GET'])]
    public function tree(string $portalId): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);
        $tree = $this->treeHandler->handle(new GetPageTreeQuery($portalId));
        return $this->json(['data' => array_map(fn ($n) => $n->toArray(), $tree)]);
    }
    /** POST /portals/{portalId}/pages */
    #[Route('/portals/{portalId}/pages', name: 'pages_create', methods: ['POST'])]
    public function create(string $portalId, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $req = CreatePageRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dto = $this->createHandler->handle(new CreatePageCommand(
                portalId:         $portalId,
                title:            $req->title,
                slug:             $req->slug,
                parentId:         $req->parentId,
                layoutTemplateId: $req->layoutTemplateId,
                meta:             $req->meta,
                sortOrder:        $req->sortOrder,
            ));
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }
    /** POST /pages/{id}/move */
    #[Route('/pages/{id}/move', name: 'pages_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->moveHandler->handle(new MovePageCommand(id: $id, parentId: $data['parent_id'] ?? null));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** POST /pages/{id}/publish */
    #[Route('/pages/{id}/publish', name: 'pages_publish', methods: ['POST'])]
    public function publish(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $dto = $this->publishHandler->handle(new PublishPageCommand($id));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** POST /pages/{id}/unpublish */
    #[Route('/pages/{id}/unpublish', name: 'pages_unpublish', methods: ['POST'])]
    public function unpublish(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        try {
            $dto = $this->unpublishHandler->handle(new UnpublishPageCommand($id));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }
    /** PATCH /pages/{id}/css */
    #[Route('/pages/{id}/css', name: 'pages_set_css', methods: ['PATCH'])]
    public function setCss(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        try {
            $dto = $this->setCssHandler->handle(new SetPageCustomCssCommand(
                pageId: $id,
                css:    isset($data['css']) ? (string) $data['css'] : null,
            ));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }
        return $this->json(['data' => $dto->toArray()]);
    }

    /** PATCH /pages/{id}/acl */
    #[Route('/pages/{id}/acl', name: 'pages_set_acl', methods: ['PATCH'])]
    public function setAcl(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::JANUS_100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data  = json_decode($request->getContent(), true) ?? [];
        $rules = $data['rules'] ?? [];
        if (!is_array($rules)) {
            return $this->json($this->validationError('"rules" must be an array.'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $dtos = $this->setAclHandler->handle(new SetPageAclCommand(pageId: $id, rules: $rules));
        } catch (PageNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return $this->json($this->validationError($e->getMessage()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->json(['data' => array_map(fn ($dto) => $dto->toArray(), $dtos)]);
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
