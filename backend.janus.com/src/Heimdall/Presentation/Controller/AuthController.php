<?php

declare(strict_types=1);

namespace App\Heimdall\Presentation\Controller;

use App\Heimdall\Application\DTO\AuthResponse;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Heimdall\Infrastructure\JWT\JwtService;
use App\Users\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth', name: 'auth_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly JwtService                 $jwtService,
        private readonly UserRepository             $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * POST /auth/login
     * Returns a JWT access token for valid credentials.
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->json(['error' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        $user->touchLastAccess();
        $this->userRepository->save($user);

        $accessToken = $this->jwtService->issueAccessToken($user);

        return $this->json((new AuthResponse($accessToken))->toArray());
    }

    /**
     * POST /auth/logout
     * Client-side token invalidation (stateless — token expires naturally).
     * Extend with a refresh token blocklist if needed.
     */
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['message' => 'Logged out successfully.']);
    }

    /**
     * GET /auth/me
     * Returns the current authenticated user's info.
     */
    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Users\Domain\Entity\User $user */
        $user = $this->getUser();

        return $this->json([
            'id'         => (string) $user->getId(),
            'email'      => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name'  => $user->getLastName(),
            'roles'      => $user->getRoles(),
        ]);
    }

    /**
     * POST /auth/password/request
     * Triggers a password-reset email (mailer integration TBD).
     */
    #[Route('/password/request', name: 'password_request', methods: ['POST'])]
    public function passwordRequest(Request $request): JsonResponse
    {
        $data  = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        if (empty($email)) {
            return $this->json(['error' => 'Email is required.'], Response::HTTP_BAD_REQUEST);
        }

        // Always return 200 to avoid user enumeration attacks.
        // The actual email dispatch will be handled by a Messenger message.
        return $this->json(['message' => 'If an account exists for that email, a reset link will be sent.']);
    }
}
