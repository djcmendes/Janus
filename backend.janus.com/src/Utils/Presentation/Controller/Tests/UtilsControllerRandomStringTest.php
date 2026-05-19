<?php

/**
 * @file UtilsControllerRandomStringTest.php
 *
 * Tests for UtilsController::randomString().
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
 * Verifies randomString() returns a string of the requested length
 * and enforces authorization.
 */
#[CoversClass(className: UtilsController::class)]
#[CoversMethod(UtilsController::class, 'randomString')]
final class UtilsControllerRandomStringTest extends UtilsControllerTest
{
    public function testRandomStringReturns200(): void
    {
        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $response   = $controller->randomString($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testRandomStringResponseHasDataKey(): void
    {
        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertArrayHasKey('data', $body);
        $this->assertArrayHasKey('random', $body['data']);
    }

    public function testRandomStringDefaultLengthIs32(): void
    {
        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertSame(32, strlen($body['data']['random']));
    }

    public function testRandomStringRespectsLengthParam(): void
    {
        $request    = new Request(query: ['length' => '16'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertSame(16, strlen($body['data']['random']));
    }

    public function testRandomStringClampsLengthToMin1(): void
    {
        $request    = new Request(query: ['length' => '0'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertSame(1, strlen($body['data']['random']));
    }

    public function testRandomStringClampsLengthToMax256(): void
    {
        $request    = new Request(query: ['length' => '999'], server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertSame(256, strlen($body['data']['random']));
    }

    public function testRandomStringRespectsCustomCharset(): void
    {
        $request    = new Request(
            query:  ['length' => '20', 'charset' => 'abc'],
            server: ['HTTP_X_CLIENT_TYPE' => 'web'],
        );
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);
        $result     = $body['data']['random'];

        $this->assertSame(20, strlen($result));
        $this->assertMatchesRegularExpression('/^[abc]+$/', $result);
    }

    public function testRandomStringResultIsString(): void
    {
        $request    = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);
        $controller = $this->buildControllerWithAdminGuard();
        $body       = json_decode((string) $controller->randomString($request)->getContent(), true);

        $this->assertIsString($body['data']['random']);
    }

    public function testRandomStringThrowsAccessDeniedWhenNotAdmin(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->class->randomString(new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']));
    }

    public function testRandomStringThrowsWhenUnauthenticated(): void
    {
        $this->expectException(UnauthorizedException::class);

        $controller = $this->buildControllerWithUnauthenticatedGuard();
        $controller->randomString(new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']));
    }
}
