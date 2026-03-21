<?php

declare(strict_types=1);

namespace App\Files\Presentation\Controller;

use App\Files\Application\Command\DeleteFileCommand;
use App\Files\Application\Command\Handler\DeleteFileHandler;
use App\Files\Application\Command\Handler\UpdateFileHandler;
use App\Files\Application\Command\Handler\UploadFileHandler;
use App\Files\Application\Command\UpdateFileCommand;
use App\Files\Application\Command\UploadFileCommand;
use App\Files\Application\Query\GetFileByIdQuery;
use App\Files\Application\Query\GetFilesQuery;
use App\Files\Application\Query\Handler\GetFileByIdHandler;
use App\Files\Application\Query\Handler\GetFilesHandler;
use App\Files\Domain\Exception\FileNotFoundException;
use App\Files\Domain\Exception\FolderNotFoundException;
use App\Files\Presentation\DTO\UpdateFileRequest;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Enum\Client;
use App\Heimdall\Domain\Service\RequestGuard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/files', name: 'files_')]
final class FilesController extends AbstractController
{
    public function __construct(
        private readonly RequestGuard       $guard,
        private readonly GetFilesHandler    $getFilesHandler,
        private readonly GetFileByIdHandler $getFileByIdHandler,
        private readonly UploadFileHandler  $uploadFileHandler,
        private readonly UpdateFileHandler  $updateFileHandler,
        private readonly DeleteFileHandler  $deleteFileHandler,
    ) {}

    /** GET /files */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $limit    = min((int) $request->query->get('limit', 25), 100);
        $offset   = (int) $request->query->get('offset', 0);
        $folderId = $request->query->get('folder') ?: null;

        $result = $this->getFilesHandler->handle(new GetFilesQuery($limit, $offset, $folderId));

        return $this->json([
            'data' => array_map(fn ($dto) => $dto->toArray(), $result['data']),
            'meta' => ['total_count' => $result['total'], 'filter_count' => count($result['data'])],
        ]);
    }

    /** GET /files/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'], priority: -1)]
    public function get(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $dto = $this->getFileByIdHandler->handle(new GetFileByIdQuery($id));
        } catch (FileNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** POST /files — multipart/form-data upload */
    #[Route('', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $uploaded = $request->files->get('file');

        if ($uploaded === null) {
            return $this->json(
                $this->validationError('No file uploaded. Send the file under the "file" field.'),
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $userId = $this->guard->validate_authenticated_user_id();

        try {
            $dto = $this->uploadFileHandler->handle(new UploadFileCommand(
                file:       $uploaded,
                title:      $request->request->get('title'),
                folderId:   $request->request->get('folder'),
                uploadedBy: $userId,
            ));
        } catch (FolderNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return $this->json($this->error($e->getMessage(), 'UPLOAD_FAILED'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['data' => $dto->toArray()], Response::HTTP_CREATED);
    }

    /** PATCH /files/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'], priority: -1)]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        $req = UpdateFileRequest::fromArray(json_decode($request->getContent(), true) ?? []);

        try {
            $dto = $this->updateFileHandler->handle(new UpdateFileCommand(
                id:               $id,
                title:            $req->title,
                filenameDownload: $req->filenameDownload,
                folderId:         $req->folderId,
            ));
        } catch (FileNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        } catch (FolderNotFoundException $e) {
            return $this->json($this->notFound($e->getMessage()), Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $dto->toArray()]);
    }

    /** DELETE /files/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], priority: -1)]
    public function delete(string $id): JsonResponse
    {
        $this->guard->validate_webservice_request(ApiVersion::V100, ApiScope::AUTHENTICATED);
        $this->guard->authorize(Client::WEB, Client::IOS, Client::ANDROID);

        try {
            $this->deleteFileHandler->handle(new DeleteFileCommand($id));
        } catch (FileNotFoundException $e) {
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

    private function error(string $message, string $code): array
    {
        return ['errors' => [['message' => $message, 'extensions' => ['code' => $code]]]];
    }
}
