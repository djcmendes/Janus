<?php

declare(strict_types=1);

namespace App\Heimdall\Presentation\Controller;

use App\Heimdall\Application\DTO\AuthResponse;
use App\Heimdall\Domain\Enum\ApiScope;
use App\Heimdall\Domain\Enum\ApiVersion;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Heimdall\Domain\Message\PasswordResetEmailMessage;
use App\Heimdall\Domain\Service\TotpService;
use App\Heimdall\Infrastructure\JWT\JwtService;
use App\Users\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth', name: 'auth_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly JwtService                  $jwtService,
        private readonly UserRepository              $userRepository,
        private readonly UserPasswordHasherInterface  $passwordHasher,
        private readonly MessageBusInterface          $bus,
        private readonly TotpService                 $totpService,
        private readonly string                      $appBaseUrl,
    ) {}

    // ── Standard auth ──────────────────────────────────────────────────────

    /**
     * POST /auth/login
     * Returns JWT access + refresh tokens. If TFA is enabled, returns a
     * short-lived tfa_pending token and {tfa_required: true} instead.
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->json(['errors' => [['message' => 'Email and password are required.']]], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        if ($user->isTotpEnabled()) {
            $tfaPendingToken = $this->jwtService->issueTfaPendingToken($user);
            return $this->json([
                'data' => [
                    'tfa_required' => true,
                    'tfa_token'    => $tfaPendingToken,
                ],
            ]);
        }

        $user->touchLastAccess();
        $this->userRepository->save($user);

        $accessToken  = $this->jwtService->issueAccessToken($user);
        $refreshToken = $this->jwtService->issueRefreshToken($user);

        return $this->json((new AuthResponse($accessToken, refreshToken: $refreshToken))->toArray());
    }

    /**
     * POST /auth/refresh
     * Exchanges a valid refresh token for a new access + refresh token pair.
     */
    #[Route('/refresh', name: 'refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data         = json_decode($request->getContent(), true);
        $refreshToken = $data['refresh_token'] ?? '';

        if (empty($refreshToken)) {
            return $this->json(['errors' => [['message' => 'refresh_token is required.']]], Response::HTTP_BAD_REQUEST);
        }

        $email = $this->jwtService->decodeTokenOfType($refreshToken, 'refresh');

        if ($email === null) {
            throw new UnauthorizedException('Invalid or expired refresh token.');
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new UnauthorizedException('User not found.');
        }

        $newAccessToken  = $this->jwtService->issueAccessToken($user);
        $newRefreshToken = $this->jwtService->issueRefreshToken($user);

        return $this->json((new AuthResponse($newAccessToken, refreshToken: $newRefreshToken))->toArray());
    }

    /**
     * POST /auth/logout
     * Client-side token invalidation (stateless — tokens expire naturally).
     */
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['data' => ['message' => 'Logged out successfully.']]);
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
            'data' => [
                'id'           => (string) $user->getId(),
                'email'        => $user->getEmail(),
                'first_name'   => $user->getFirstName(),
                'last_name'    => $user->getLastName(),
                'roles'        => $user->getRoles(),
                'totp_enabled' => $user->isTotpEnabled(),
            ],
        ]);
    }

    /**
     * POST /auth/password/request
     * Dispatches a password-reset email via Messenger.
     */
    #[Route('/password/request', name: 'password_request', methods: ['POST'])]
    public function passwordRequest(Request $request): JsonResponse
    {
        $data  = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        if (empty($email)) {
            return $this->json(['errors' => [['message' => 'Email is required.']]], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return $this->json(['data' => ['message' => 'If an account exists for that email, a reset link will be sent.']]);
        }

        $resetToken = $this->jwtService->issuePasswordResetToken($user);

        $this->bus->dispatch(new PasswordResetEmailMessage(
            recipientEmail: $user->getEmail(),
            resetToken:     $resetToken,
            appBaseUrl:     $this->appBaseUrl,
        ));

        return $this->json(['data' => ['message' => 'If an account exists for that email, a reset link will be sent.']]);
    }

    /**
     * POST /auth/password/reset
     * Validates the reset token and sets a new password.
     */
    #[Route('/password/reset', name: 'password_reset', methods: ['POST'])]
    public function passwordReset(Request $request): JsonResponse
    {
        $data     = json_decode($request->getContent(), true);
        $token    = $data['token']    ?? '';
        $password = $data['password'] ?? '';

        if (empty($token) || empty($password)) {
            return $this->json(['errors' => [['message' => 'token and password are required.']]], Response::HTTP_BAD_REQUEST);
        }

        if (strlen($password) < 8) {
            return $this->json(['errors' => [['message' => 'Password must be at least 8 characters.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $this->jwtService->decodeTokenOfType($token, 'reset');

        if ($email === null) {
            throw new UnauthorizedException('Invalid or expired reset token.');
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new UnauthorizedException('User not found.');
        }

        $hashed = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashed);
        $this->userRepository->save($user);

        return $this->json(['data' => ['message' => 'Password updated successfully.']]);
    }

    // ── Two-Factor Authentication ──────────────────────────────────────────

    /**
     * GET /auth/tfa/setup
     * Authenticated. Generates a fresh TOTP secret, stores it (not yet enabled),
     * and returns the provisioning URI for QR-code display plus the raw secret
     * for manual entry.
     */
    #[Route('/tfa/setup', name: 'tfa_setup', methods: ['GET'])]
    public function tfaSetup(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Users\Domain\Entity\User $user */
        $user   = $this->getUser();
        $secret = $this->totpService->generateSecret();

        $user->storeTotpSecret($secret);
        $this->userRepository->save($user);

        return $this->json([
            'data' => [
                'provisioning_uri' => $this->totpService->buildProvisioningUri($user->getEmail(), $secret),
                'secret'           => $secret,
            ],
        ]);
    }

    /**
     * POST /auth/tfa/enable
     * Authenticated. Verifies the submitted OTP against the stored (not yet
     * enabled) secret, then marks TFA as enabled on the user.
     */
    #[Route('/tfa/enable', name: 'tfa_enable', methods: ['POST'])]
    public function tfaEnable(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Users\Domain\Entity\User $user */
        $user = $this->getUser();
        $otp  = (string) (json_decode($request->getContent(), true)['otp'] ?? '');

        if ($otp === '') {
            return $this->json(['errors' => [['message' => 'otp is required.']]], Response::HTTP_BAD_REQUEST);
        }

        $secret = $user->getTotpSecret();

        if ($secret === null) {
            return $this->json(['errors' => [['message' => 'Call GET /auth/tfa/setup first.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$this->totpService->verifyCode($secret, $otp)) {
            return $this->json(['errors' => [['message' => 'Invalid OTP code.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->enableTotp($secret);
        $this->userRepository->save($user);

        return $this->json(['data' => ['message' => 'Two-factor authentication enabled.']]);
    }

    /**
     * POST /auth/tfa/disable
     * Authenticated. Verifies the OTP then clears TFA from the account.
     */
    #[Route('/tfa/disable', name: 'tfa_disable', methods: ['POST'])]
    public function tfaDisable(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Users\Domain\Entity\User $user */
        $user = $this->getUser();
        $otp  = (string) (json_decode($request->getContent(), true)['otp'] ?? '');

        if ($otp === '') {
            return $this->json(['errors' => [['message' => 'otp is required.']]], Response::HTTP_BAD_REQUEST);
        }

        $secret = $user->getTotpSecret();

        if ($secret === null || !$user->isTotpEnabled()) {
            return $this->json(['errors' => [['message' => 'TFA is not enabled on this account.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$this->totpService->verifyCode($secret, $otp)) {
            return $this->json(['errors' => [['message' => 'Invalid OTP code.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->disableTotp();
        $this->userRepository->save($user);

        return $this->json(['data' => ['message' => 'Two-factor authentication disabled.']]);
    }

    /**
     * POST /auth/tfa/verify
     * PUBLIC scope. Accepts a tfa_pending JWT + OTP code.
     * On success returns a full access + refresh token pair.
     */
    #[Route('/tfa/verify', name: 'tfa_verify', methods: ['POST'])]
    public function tfaVerify(Request $request): JsonResponse
    {
        $data      = json_decode($request->getContent(), true);
        $tfaToken  = $data['tfa_token'] ?? '';
        $otp       = (string) ($data['otp'] ?? '');

        if (empty($tfaToken) || $otp === '') {
            return $this->json(['errors' => [['message' => 'tfa_token and otp are required.']]], Response::HTTP_BAD_REQUEST);
        }

        $email = $this->jwtService->decodeTokenOfType($tfaToken, 'tfa_pending');

        if ($email === null) {
            throw new UnauthorizedException('Invalid or expired TFA token.');
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null || !$user->isTotpEnabled() || $user->getTotpSecret() === null) {
            throw new UnauthorizedException('TFA not configured for this account.');
        }

        if (!$this->totpService->verifyCode($user->getTotpSecret(), $otp)) {
            return $this->json(['errors' => [['message' => 'Invalid OTP code.']]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->touchLastAccess();
        $this->userRepository->save($user);

        $accessToken  = $this->jwtService->issueAccessToken($user);
        $refreshToken = $this->jwtService->issueRefreshToken($user);

        return $this->json((new AuthResponse($accessToken, refreshToken: $refreshToken))->toArray());
    }
}
