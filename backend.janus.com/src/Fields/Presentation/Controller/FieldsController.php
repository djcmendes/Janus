<?php

declare(strict_types=1);

namespace App\Fields\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fields', name: 'fields_')]
final class FieldsController extends AbstractController
{
    /** GET /fields */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        return $this->json(['data' => [], 'meta' => ['total_count' => 0]]);
    }

    /** GET /fields/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        return $this->json(['data' => null], Response::HTTP_NOT_FOUND);
    }

    /** POST /fields */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->json(['data' => []], Response::HTTP_CREATED);
    }

    /** PATCH /fields/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request): JsonResponse
    {
        return $this->json(['data' => []]);
    }

    /** DELETE /fields/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
