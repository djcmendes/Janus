<?php

declare(strict_types=1);

namespace App\Users\Presentation\Controller;

use App\Users\Domain\Entity\User;
use App\Users\Infrastructure\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users_')]
final class UsersController extends AbstractController
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    /** GET /users */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $limit  = min((int) $request->query->get('limit', 25), 100);
        $offset = (int) $request->query->get('offset', 0);

        $users = $this->userRepository->findBy(['deletedAt' => null], ['createdAt' => 'DESC'], $limit, $offset);
        $total = $this->userRepository->count(['deletedAt' => null]);

        return $this->json([
            'data' => array_map(fn (User $u) => $this->serialize($u), $users),
            'meta' => ['total_count' => $total, 'filter_count' => count($users)],
        ]);
    }

    /** GET /users/:id */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        $user = $this->userRepository->findActiveById($id);

        if ($user === null) {
            return $this->json(['errors' => [['message' => 'User not found.']]], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $this->serialize($user)]);
    }

    /** POST /users */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['errors' => [['message' => 'email and password are required.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($this->userRepository->findByEmail($data['email'])) {
            return $this->json(['errors' => [['message' => 'A user with this email already exists.']]], Response::HTTP_CONFLICT);
        }

        $user = new User($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        if (!empty($data['first_name'])) $user->setFirstName($data['first_name']);
        if (!empty($data['last_name']))  $user->setLastName($data['last_name']);
        if (!empty($data['roles']))      $user->setRoles($data['roles']);

        $this->userRepository->save($user);

        return $this->json(['data' => $this->serialize($user)], Response::HTTP_CREATED);
    }

    /** PATCH /users/:id */
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(string $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->userRepository->findActiveById($id);
        if ($user === null) {
            return $this->json(['errors' => [['message' => 'User not found.']]], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        if (isset($data['first_name'])) $user->setFirstName($data['first_name']);
        if (isset($data['last_name']))  $user->setLastName($data['last_name']);
        if (isset($data['roles']))      $user->setRoles($data['roles']);
        if (!empty($data['password']))  $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $this->userRepository->save($user);

        return $this->json(['data' => $this->serialize($user)]);
    }

    /** DELETE /users/:id */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->userRepository->findActiveById($id);
        if ($user === null) {
            return $this->json(['errors' => [['message' => 'User not found.']]], Response::HTTP_NOT_FOUND);
        }

        $user->softDelete();
        $this->userRepository->save($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize(User $user): array
    {
        return [
            'id'             => (string) $user->getId(),
            'email'          => $user->getEmail(),
            'first_name'     => $user->getFirstName(),
            'last_name'      => $user->getLastName(),
            'roles'          => $user->getRoles(),
            'last_access_at' => $user->getLastAccessAt()?->format(\DateTimeInterface::ATOM),
            'created_at'     => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
