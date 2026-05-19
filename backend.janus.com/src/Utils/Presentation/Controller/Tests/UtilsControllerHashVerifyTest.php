<?php

/**
 * @file UtilsControllerHashVerifyTest.php
 *
 * Tests for UtilsController::hashVerify().
 *
 * @package App\Utils\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Utils\Presentation\Controller\Tests;

use App\Heimdall\Domain\Exception\UnauthorizedException;
use App\Utils\Presentation\Controller\UtilsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Verifies hashVerify() correctly validates bcrypt hashes and enforces authorization.
 */
#[CoversClass(className: UtilsController::class)]
#[CoversMethod(UtilsController::class, 'hashVerify')]
final class UtilsControllerHashVerifyTest extends UtilsControllerTest
{
    private const string KNOWN_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    // password_hash('password', PASSWORD_BCRYPT) — stable bcrypt hash of "password"

    public function testHashVerifyReturns200(): void
    {
        $request    = new Request(
            query:  ['value' => 'password', 'hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->hashVerify($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHashVerifyReturnsTrueForMatchingHash(): void
    {
        $request    = new Request(
            query:  ['value' => 'password', 'hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashVerify($request)->getContent(), true);

        $this->assertTrue($body['data']['valid']);
    }

    public function testHashVerifyReturnsFalseForWrongValue(): void
    {
        $request    = new Request(
            query:  ['value' => 'wrong', 'hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashVerify($request)->getContent(), true);

        $this->assertFalse($body['data']['valid']);
    }

    public function testHashVerifyResponseHasDataKey(): void
    {
        $request    = new Request(
            query:  ['value' => 'password', 'hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashVerify($request)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('valid', $body['data']);
    }

    public function testHashVerifyReturns422WhenValueMissing(): void
    {
        $request    = new Request(
            query:  ['hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->hashVerify($request);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testHashVerifyReturns422WhenHashMissing(): void
    {
        $request    = new Request(
            query:  ['value' => 'password'],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->hashVerify($request);

        $this->assertSame(422, $response->getStatusCode());
    }

    public function testHashVerifyThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request(
            query:  ['value' => 'password', 'hash' => self::KNOWN_HASH],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $this->class->hashVerify($request);
    }

    public function testHashVerifyThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->hashVerify(new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']));
    }
}
