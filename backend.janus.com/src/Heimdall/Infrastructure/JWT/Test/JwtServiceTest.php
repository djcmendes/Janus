<?php

declare(strict_types=1);

namespace App\Heimdall\Infrastructure\JWT\Test;

use App\Heimdall\Infrastructure\JWT\JwtService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtServiceTest extends TestCase
{
    private JWTTokenManagerInterface $jwtManager;
    private JWTEncoderInterface $jwtEncoder;
    private JwtService $service;

    protected function setUp(): void
    {
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->service    = new JwtService($this->jwtManager, $this->jwtEncoder, refreshTtl: 604800);
    }

    // ── issueAccessToken ──────────────────────────────────────────────────

    public function testIssueAccessTokenDelegatesToJwtManager(): void
    {
        $user = $this->createMock(UserInterface::class);

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('access.token.value');

        $result = $this->service->issueAccessToken($user);

        $this->assertSame('access.token.value', $result);
    }

    // ── issueRefreshToken ─────────────────────────────────────────────────

    public function testIssueRefreshTokenEncodesCorrectPayload(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $this->jwtEncoder
            ->expects($this->once())
            ->method('encode')
            ->with($this->callback(function (array $payload): bool {
                return $payload['sub']  === 'user@example.com'
                    && $payload['type'] === 'refresh'
                    && isset($payload['iat'])
                    && isset($payload['exp'])
                    && $payload['exp'] > time();
            }))
            ->willReturn('refresh.token.value');

        $result = $this->service->issueRefreshToken($user);

        $this->assertSame('refresh.token.value', $result);
    }

    public function testIssueRefreshTokenTtlIsApplied(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $capturedPayload = null;
        $this->jwtEncoder
            ->method('encode')
            ->willReturnCallback(function (array $payload) use (&$capturedPayload): string {
                $capturedPayload = $payload;
                return 'token';
            });

        $before = time();
        $this->service->issueRefreshToken($user);
        $after = time();

        $this->assertGreaterThanOrEqual($before + 604800, $capturedPayload['exp']);
        $this->assertLessThanOrEqual($after + 604800, $capturedPayload['exp']);
    }

    // ── issuePasswordResetToken ───────────────────────────────────────────

    public function testIssuePasswordResetTokenEncodesResetType(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $this->jwtEncoder
            ->expects($this->once())
            ->method('encode')
            ->with($this->callback(function (array $payload): bool {
                return $payload['sub']  === 'user@example.com'
                    && $payload['type'] === 'reset'
                    && $payload['exp']  <= time() + 3601; // ~1 hour
            }))
            ->willReturn('reset.token.value');

        $result = $this->service->issuePasswordResetToken($user);

        $this->assertSame('reset.token.value', $result);
    }

    // ── decodeTokenOfType ─────────────────────────────────────────────────

    public function testDecodeTokenOfTypeReturnsSubjectWhenTypeMatches(): void
    {
        $this->jwtEncoder
            ->method('decode')
            ->willReturn(['sub' => 'user@example.com', 'type' => 'refresh']);

        $result = $this->service->decodeTokenOfType('some.token', 'refresh');

        $this->assertSame('user@example.com', $result);
    }

    public function testDecodeTokenOfTypeReturnsNullWhenTypeMismatch(): void
    {
        $this->jwtEncoder
            ->method('decode')
            ->willReturn(['sub' => 'user@example.com', 'type' => 'access']);

        $result = $this->service->decodeTokenOfType('some.token', 'refresh');

        $this->assertNull($result);
    }

    public function testDecodeTokenOfTypeReturnsNullOnDecoderException(): void
    {
        $this->jwtEncoder
            ->method('decode')
            ->willThrowException(new \RuntimeException('Invalid token'));

        $result = $this->service->decodeTokenOfType('bad.token', 'refresh');

        $this->assertNull($result);
    }

    public function testDecodeTokenOfTypeReturnsNullWhenSubMissing(): void
    {
        $this->jwtEncoder
            ->method('decode')
            ->willReturn(['type' => 'refresh']);

        $result = $this->service->decodeTokenOfType('some.token', 'refresh');

        $this->assertNull($result);
    }

    // ── decode ────────────────────────────────────────────────────────────

    public function testDecodeReturnsParsedPayload(): void
    {
        $payload = ['username' => 'user@example.com', 'roles' => ['ROLE_USER']];

        $this->jwtManager
            ->method('parse')
            ->willReturn($payload);

        $result = $this->service->decode('some.token');

        $this->assertSame($payload, $result);
    }

    public function testDecodeReturnsNullOnException(): void
    {
        $this->jwtManager
            ->method('parse')
            ->willThrowException(new \RuntimeException('Token expired'));

        $result = $this->service->decode('expired.token');

        $this->assertNull($result);
    }
}
