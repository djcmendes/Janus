<?php

/**
 * @file DeploymentsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * scenario-builder helpers for all DeploymentsController test cases.
 *
 * Strategy: All injected dependencies are final classes and cannot be mocked directly.
 * Each is instantiated as a real object backed by mocked interfaces. Tests control
 * behaviour at the dependency layer — no bypass extension required.
 *
 * @package App\Deployments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller\Tests;

use App\Deployments\Application\Command\Handler\CreateDeploymentHandler;
use App\Deployments\Application\Command\Handler\DeleteDeploymentHandler;
use App\Deployments\Application\Command\Handler\TriggerDeploymentHandler;
use App\Deployments\Application\Query\Handler\GetDeploymentByIdHandler;
use App\Deployments\Application\Query\Handler\GetDeploymentsHandler;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use App\Deployments\Domain\Repository\DeploymentRepositoryInterface;
use App\Deployments\Presentation\Controller\DeploymentsController;
use App\Heimdall\Domain\Service\RequestGuard;
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
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Common setup, teardown, shared instances, and scenario-builder helpers
 * for all DeploymentsController test suites.
 */
#[CoversClass(DeploymentsController::class)]
abstract class DeploymentsControllerTest extends TestCase
{
    /** @var string UUID returned by the fake authenticated user's getId() method. */
    public const string AUTH_USER_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /** @var MockObject&TokenStorageInterface */
    protected MockObject $tokenStorage;

    /** @var MockObject&RequestStack */
    protected MockObject $requestStack;

    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $providerRepository;

    /** @var MockObject&DeploymentRepositoryInterface */
    protected MockObject $deploymentRepository;

    /** @var MockObject&HttpClientInterface */
    protected MockObject $httpClient;

    /** @var MockObject&ResponseInterface */
    protected MockObject $httpResponse;

    /** @var MockObject&ContainerInterface */
    protected MockObject $container;

    /** @var MockObject&AuthorizationCheckerInterface */
    protected MockObject $authorizationChecker;

    /** @var RequestGuard */
    protected RequestGuard $guard;

    /** @var GetDeploymentsHandler */
    protected GetDeploymentsHandler $getDeploymentsHandler;

    /** @var GetDeploymentByIdHandler */
    protected GetDeploymentByIdHandler $getDeploymentByIdHandler;

    /** @var CreateDeploymentHandler */
    protected CreateDeploymentHandler $createDeploymentHandler;

    /** @var DeleteDeploymentHandler */
    protected DeleteDeploymentHandler $deleteDeploymentHandler;

    /** @var TriggerDeploymentHandler */
    protected TriggerDeploymentHandler $triggerDeploymentHandler;

    /** @var DeploymentsController */
    protected DeploymentsController $class;

    /** @var ReflectionClass<DeploymentsController> */
    protected ReflectionClass $reflection;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $user = new class implements UserInterface {
            public function getId(): string { return DeploymentsControllerTest::AUTH_USER_UUID; }
            public function getRoles(): array { return ['ROLE_ADMIN']; }
            public function eraseCredentials(): void {}
            public function getUserIdentifier(): string { return 'admin@example.com'; }
        };

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->tokenStorage->method('getToken')->willReturn($token);

        $webRequest = new Request(server: ['HTTP_X_CLIENT_TYPE' => 'web']);

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($webRequest);

        $this->providerRepository   = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->deploymentRepository = $this->createMock(DeploymentRepositoryInterface::class);

        $this->httpResponse = $this->createMock(ResponseInterface::class);
        $this->httpClient   = $this->createMock(HttpClientInterface::class);

        $this->guard                    = new RequestGuard(tokenStorage: $this->tokenStorage, requestStack: $this->requestStack);
        $this->getDeploymentsHandler    = new GetDeploymentsHandler(repository: $this->providerRepository);
        $this->getDeploymentByIdHandler = new GetDeploymentByIdHandler(repository: $this->providerRepository);
        $this->createDeploymentHandler  = new CreateDeploymentHandler(repository: $this->providerRepository);
        $this->deleteDeploymentHandler  = new DeleteDeploymentHandler(repository: $this->providerRepository);
        $this->triggerDeploymentHandler = new TriggerDeploymentHandler(
            providerRepository:   $this->providerRepository,
            deploymentRepository: $this->deploymentRepository,
            httpClient:           $this->httpClient,
        );

        // All Deployments actions require ROLE_ADMIN — grant it by default so
        // happy-path tests can reach the actual handler logic.
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method('isGranted')->willReturn(true);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $this->container->method('get')->willReturnMap([
            ['security.authorization_checker', $this->authorizationChecker],
        ]);

        $this->class = new DeploymentsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(DeploymentsController::class);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->tokenStorage,
            $this->requestStack,
            $this->providerRepository,
            $this->deploymentRepository,
            $this->httpClient,
            $this->httpResponse,
            $this->container,
            $this->authorizationChecker,
            $this->guard,
            $this->getDeploymentsHandler,
            $this->getDeploymentByIdHandler,
            $this->createDeploymentHandler,
            $this->deleteDeploymentHandler,
            $this->triggerDeploymentHandler,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Returns a controller backed by a guard whose token storage returns no token,
     * causing validate_webservice_request() to throw UnauthorizedException.
     *
     * @return DeploymentsController
     */
    protected function buildControllerWithUnauthenticatedGuard(): DeploymentsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn(null);

        $guard = new RequestGuard(tokenStorage: $tokenStorage, requestStack: $this->requestStack);

        $controller = new DeploymentsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns a controller backed by a guard and container where ROLE_ADMIN is granted.
     *
     * @return DeploymentsController
     */
    protected function buildControllerWithAdminGuard(): DeploymentsController
    {
        $authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authChecker->method('isGranted')->willReturn(true);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);
        $container->method('get')->willReturnMap([
            ['security.authorization_checker', $authChecker],
        ]);

        $controller = new DeploymentsController(guard: $this->guard);
        $controller->setContainer(container: $container);

        return $controller;
    }

    /**
     * Creates a fully-populated DeploymentProvider entity for use in test assertions.
     *
     * @return DeploymentProvider
     */
    protected function makeProvider(): DeploymentProvider
    {
        return DeploymentProvider::reconstitute(
            id:        'bbbbbbbb-0000-7000-8000-000000000001',
            name:      'Netlify Production',
            type:      DeploymentProviderType::NETLIFY,
            url:       'https://api.netlify.com/build_hooks/abc123',
            options:   null,
            isActive:  true,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
    }
}
