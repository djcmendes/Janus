<?php

/**
 * @file UtilsControllerHashGenerateTest.php
 *
 * Tests for UtilsController::hashGenerate().
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
 * Verifies hashGenerate() returns a bcrypt hash and enforces authorization.
 */
#[CoversClass(className: UtilsController::class)]
#[CoversMethod(UtilsController::class, 'hashGenerate')]
final class UtilsControllerHashGenerateTest extends UtilsControllerTest
{
    public function testHashGenerateReturns200(): void
    {
        $request    = new Request(query: ['value' => 'secret'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->hashGenerate($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHashGenerateResponseHasDataKey(): void
    {
        $request    = new Request(query: ['value' => 'secret'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashGenerate($request)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
    }

    public function testHashGenerateResponseDataHasHashKey(): void
    {
        $request    = new Request(query: ['value' => 'secret'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashGenerate($request)->getContent(), true);

        $this->assertArrayHasKey('hash', $body['data']);
    }

    public function testHashGenerateReturnsBcryptHash(): void
    {
        $request    = new Request(query: ['value' => 'secret'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->hashGenerate($request)->getContent(), true);

        $this->assertTrue(password_verify('secret', $body['data']['hash']));
    }

    public function testHashGenerateReturns422WhenValueMissing(): void
    {
        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->hashGenerate($request);

        $this->assertSame(422, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('VALIDATION_ERROR', $body['errors'][0]['extensions']['code']);
    }

    public function testHashGenerateThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request(query: ['value' => 'secret'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $this->class->hashGenerate($request);
    }

    public function testHashGenerateThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->hashGenerate(new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']));
    }
}
