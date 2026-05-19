<?php

/**
 * @file SchemaControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all SchemaController test cases.
 *
 * Strategy: RequestGuard is final — instantiated as a real object backed by
 * mocked TokenStorageInterface and RequestStack. GetSchemaSnapshotHandler,
 * ApplySchemaHandler, SchemaSnapshotService, SchemaDiffService are all final;
 * they are instantiated as real objects backed by mocked repository interfaces.
 * symfony/serializer and symfony/validator are not installed locally; local
 * stub interfaces (SchemaSerializerStub, SchemaValidatorStub) enable mocking
 * without those packages.
 *
 * @package App\Schema\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Schema\Presentation\Controller\Tests;

use App\Heimdall\Domain\Service\RequestGuard;
use App\Schema\Application\Command\Handler\ApplySchemaHandlerInterface;
use App\Schema\Application\Query\Handler\GetSchemaSnapshotHandlerInterface;
use App\Schema\Domain\Service\SchemaDiffServiceInterface;
use App\Schema\Domain\Service\SchemaSnapshotServiceInterface;
use App\Schema\Presentation\Controller\SchemaController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Minimal serializer stub — symfony/serializer not installed locally.
 */
interface SchemaSerializerStub
{
    public function serialize(mixed $data, string $format, array $context = []): string;
    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed;
}

/**
 * Minimal validator stub — symfony/validator not installed locally.
 */
interface SchemaValidatorStub
{
    public function validate(mixed $value, mixed $constraints = null, mixed $groups = null): \Countable;
}

/**
 * Common setup, teardown, and helpers for all SchemaController test suites.
 */
#[CoversClass(className: SchemaController::class)]
abstract class SchemaControllerTest extends TestCase
{
    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    /** @var MockObject&SchemaSerializerStub */
    protected MockObject $serializer;

    /** @var MockObject&SchemaValidatorStub */
    protected MockObject $validator;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&GetSchemaSnapshotHandlerInterface */
    protected MockObject $snapshotHandler;

    /** @var array<string, mixed> Mutable return value for snapshotHandler::handle() */
    protected array $snapshotHandlerReturn = ['version' => 1, 'collections' => [], 'relations' => []];

    /** @var MockObject&ApplySchemaHandlerInterface */
    protected MockObject $applyHandler;

    /** @var array<string, mixed> Mutable return value for applyHandler::handle() */
    protected array $applyHandlerReturn = ['applied' => [], 'skipped' => []];

    /** @var \Throwable|null When set, applyHandler::handle() throws this instead of returning */
    protected ?\Throwable $applyHandlerThrow = null;

    /** @var MockObject&SchemaSnapshotServiceInterface */
    protected MockObject $snapshotService;

    /** @var MockObject&SchemaDiffServiceInterface */
    protected MockObject $diffService;

    /** @var RequestGuard */
    protected RequestGuard $guard;

    /** @var SchemaController */
    protected SchemaController $class;

    /** @var ReflectionClass<SchemaController> */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getRoles(): array { return ['ROLE_USER']; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'test@example.com'; }
        };

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->method('getToken')->willReturn($token);

        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($webRequest);

        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method('isGranted')->willReturn(false);

        $this->serializer = $this->createMock(SchemaSerializerStub::class);
        $this->serializer->method('serialize')->willReturnCallback(
            static fn(mixed $data) => (string) json_encode($data),
        );

        $this->validator = $this->createMock(SchemaValidatorStub::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', true],
            ['validator', true],
        ]);
        $this->container->method('get')->willReturnMap([
            ['security.authorization_checker', $this->authorizationChecker],
            ['serializer', $this->serializer],
            ['validator', $this->validator],
        ]);

        $this->snapshotService = $this->createMock(SchemaSnapshotServiceInterface::class);
        $this->snapshotService->method('snapshot')->willReturn([
            'version' => 1, 'collections' => [], 'relations' => [],
        ]);

        $this->diffService = $this->createMock(SchemaDiffServiceInterface::class);
        $this->diffService->method('diff')->willReturn([
            'collections' => ['create' => [], 'update' => [], 'delete' => []],
            'fields'      => ['create' => [], 'update' => [], 'delete' => []],
            'relations'   => ['create' => [], 'update' => [], 'delete' => []],
        ]);

        $this->snapshotHandlerReturn = ['version' => 1, 'collections' => [], 'relations' => []];
        $this->snapshotHandler       = $this->createMock(GetSchemaSnapshotHandlerInterface::class);
        $this->snapshotHandler->method('handle')
            ->willReturnCallback(fn() => $this->snapshotHandlerReturn);

        $this->applyHandlerReturn = ['applied' => [], 'skipped' => []];
        $this->applyHandlerThrow  = null;
        $this->applyHandler       = $this->createMock(ApplySchemaHandlerInterface::class);
        $this->applyHandler->method('handle')
            ->willReturnCallback(function () {
                if ($this->applyHandlerThrow !== null) {
                    throw $this->applyHandlerThrow;
                }
                return $this->applyHandlerReturn;
            });

        $this->guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $this->requestStack,
        );

        $this->class = new SchemaController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(SchemaController::class);
    }

    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->authorizationChecker,
            $this->serializer,
            $this->validator,
            $this->container,
            $this->snapshotHandler,
            $this->applyHandler,
            $this->snapshotService,
            $this->diffService,
            $this->guard,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller whose authorization checker grants every ROLE_ADMIN check.
     */
    protected function buildControllerWithAdminGuard(): SchemaController
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', true],
            ['validator', true],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
            ['serializer', $this->serializer],
            ['validator', $this->validator],
        ]);

        $controller = new SchemaController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     */
    protected function buildControllerWithUnauthenticatedGuard(): SchemaController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new SchemaController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns an empty Countable suitable as the validator response when validation passes.
     */
    protected function makeEmptyViolations(): \Countable
    {
        return new class implements \Countable {
            public function count(): int { return 0; }
        };
    }

    /**
     * Builds a Symfony Request with JSON body and the X-Client-Type header set.
     *
     * @param array<string, mixed> $body
     */
    protected function jsonRequest(array $body, string $clientType = 'web'): Request
    {
        return new Request(
            server:  ['HTTP_X_CLIENT_TYPE' => $clientType],
            content: json_encode($body),
        );
    }
}
