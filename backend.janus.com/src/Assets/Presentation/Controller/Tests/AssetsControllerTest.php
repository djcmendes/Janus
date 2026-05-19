<?php

/**
 * @file AssetsControllerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, scenario-
 * builder helpers, and a File factory for all AssetsController test cases.
 *
 * Strategy: AssetsController, RequestGuard, GetAssetHandler,
 * FileStorageService, and AssetTransformService are all `final` and cannot be
 * mocked directly. They are each instantiated as real objects. Only their
 * injectable dependencies that are interfaces (FileRepositoryInterface) or
 * non-final Symfony classes (TokenStorageInterface, RequestStack,
 * ContainerInterface, AuthorizationCheckerInterface) are mocked.
 *
 * A GD-generated JPEG fixture is written to a temporary directory in setUp()
 * so the full transform pipeline can be exercised in happy-path tests.
 *
 * @package App\Assets\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Presentation\Controller\Tests;

use App\Assets\Application\Query\Handler\GetAssetHandler;
use App\Assets\Domain\Service\AssetTransformService;
use App\Assets\Presentation\Controller\AssetsController;
use App\Files\Domain\Entity\File;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Storage\FileStorageService;
use App\Heimdall\Application\Service\RequestGuard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
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
 * Common setup, teardown, fixtures, and scenario builders for all
 * AssetsController test suites.
 */
#[CoversClass(className: AssetsController::class)]
abstract class AssetsControllerTest extends TestCase
{
    /**
     * Disk filename of the JPEG fixture written to $tempDir in setUp().
     * @var string
     */
    protected const string FIXTURE_FILENAME_DISK = 'fixture-asset.jpg';

    /**
     * Mock of the domain file repository — controls what findById() returns.
     * @var MockObject&FileRepositoryInterface
     */
    protected MockObject $fileRepository;

    /**
     * Real FileStorageService backed by $tempDir.
     * @var FileStorageService
     */
    protected FileStorageService $storage;

    /**
     * Real AssetTransformService — no injected deps.
     * @var AssetTransformService
     */
    protected AssetTransformService $transformer;

    /**
     * Real GetAssetHandler backed by mocked FileRepositoryInterface and real services.
     * @var GetAssetHandler
     */
    protected GetAssetHandler $handler;

    /**
     * Mock of Symfony's token storage — controls authentication state.
     * @var MockObject&TokenStorageInterface
     */
    protected MockObject $tokenStorage;

    /**
     * Mock of Symfony's request stack — controls the X-Client-Type header.
     * @var MockObject&RequestStack
     */
    protected MockObject $requestStack;

    /**
     * Real RequestGuard backed by mocked token storage and request stack.
     * @var RequestGuard
     */
    protected RequestGuard $guard;

    /**
     * Mock of the Symfony container — satisfies AbstractController internals.
     * @var MockObject&ContainerInterface
     */
    protected MockObject $container;

    /**
     * Mock of the authorization checker — controls role-based access.
     * @var MockObject&AuthorizationCheckerInterface
     */
    protected MockObject $authorizationChecker;

    /**
     * The system under test.
     * @var AssetsController
     */
    protected AssetsController $class;

    /**
     * Reflection of AssetsController for reading private properties.
     * @var ReflectionClass<AssetsController>
     */
    protected ReflectionClass $reflection;

    /**
     * Temporary directory containing the fixture JPEG created in setUp().
     * @var string
     */
    protected string $tempDir;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/janus_test_assets_ctrl_' . uniqid(more_entropy: true);
        mkdir(directory: $this->tempDir, recursive: true);

        $img = imagecreatetruecolor(width: 16, height: 9);
        $col = imagecolorallocate(image: $img, red: 200, green: 100, blue: 50);
        imagefilledrectangle(image: $img, x1: 0, y1: 0, x2: 15, y2: 8, color: $col);
        imagejpeg(image: $img, file: $this->tempDir . '/' . self::FIXTURE_FILENAME_DISK, quality: 85);
        imagedestroy(image: $img);

        $this->fileRepository = $this->createMock(type: FileRepositoryInterface::class);

        $this->storage     = new FileStorageService(storagePath: $this->tempDir);
        $this->transformer = new AssetTransformService();
        $this->handler     = new GetAssetHandler(fileRepository: $this->fileRepository, storage: $this->storage, transformer: $this->transformer);

        $user  = $this->createMock(type: UserInterface::class);
        $token = $this->createMock(type: TokenInterface::class);
        $token->method(constraint: 'getUser')
              ->willReturn(value: $user);

        $this->tokenStorage = $this->createMock(type: TokenStorageInterface::class);
        $this->tokenStorage->method(constraint: 'getToken')
                           ->willReturn(value: $token);

        $webRequest = new Request(server: [ 'HTTP_X_CLIENT_TYPE' => 'web' ]);

        $this->requestStack = $this->createMock(type: RequestStack::class);
        $this->requestStack->method(constraint: 'getCurrentRequest')
                           ->willReturn(value: $webRequest);

        $this->guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $this->requestStack,
        );

        $this->authorizationChecker = $this->createMock(type: AuthorizationCheckerInterface::class);
        $this->authorizationChecker->method(constraint: 'isGranted')
                                   ->willReturn(value: true);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->method(constraint: 'has')->willReturnMap([
            ['security.authorization_checker', true],
            ['serializer', false],
        ]);

        $this->container->method(constraint: 'get')->willReturnMap(
            valueMap: [[ 'security.authorization_checker', $this->authorizationChecker ]]
        );

        $this->class = new AssetsController(guard: $this->guard);
        $this->class->setContainer(container: $this->container);

        $this->reflection = new ReflectionClass(AssetsController::class);
    }

    public function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            array_map('unlink', glob($this->tempDir . '/*') ?: []);
            rmdir($this->tempDir);
        }

        unset(
            $this->fileRepository,
            $this->storage,
            $this->transformer,
            $this->handler,
            $this->tokenStorage,
            $this->requestStack,
            $this->guard,
            $this->container,
            $this->authorizationChecker,
            $this->class,
            $this->reflection,
        );
    }

    /**
     * Creates a domain File entity pointing at the fixture JPEG in $tempDir.
     *
     * @param string $filenameDisk     Disk filename — must exist inside $tempDir.
     * @param string $type             MIME type declared on the record.
     * @param string $filenameDownload Original filename shown on download.
     *
     * @return File A fully-hydrated domain entity with deterministic values.
     */
    protected function makeFile(
        string $filenameDisk     = self::FIXTURE_FILENAME_DISK,
        string $type             = 'image/jpeg',
        string $filenameDownload = 'photo.jpg',
    ): File {
        return File::reconstitute(
            id:               'aaaaaaaa-0000-7000-8000-000000000001',
            storage:          'local',
            filenameDisk:     $filenameDisk,
            filenameDownload: $filenameDownload,
            title:            'Fixture Photo',
            type:             $type,
            filesize:         2048,
            width:            16,
            height:           9,
            uploadedBy:       null,
            folderId:         null,
            createdAt:        new \DateTimeImmutable('2024-01-01T00:00:00+00:00'),
            updatedAt:        null,
        );
    }

    /**
     * Returns a controller whose guard has a token storage that returns null,
     * causing validateWebserviceRequest() to throw UnauthorizedException.
     *
     * @return AssetsController A controller instance pre-wired to fail on authentication.
     * @throws Exception
     */
    protected function buildControllerWithUnauthenticatedGuard(): AssetsController
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method(constraint: 'getToken')
                     ->willReturn(null);

        $guard = new RequestGuard(
            tokenStorage: $tokenStorage,
            requestStack: $this->requestStack,
        );

        $controller = new AssetsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }

    /**
     * Returns a controller whose guard receives a request with an ANDROID
     * client type, which is not in the allowed list (WEB|CLI), causing
     * authorize() to throw UnauthorizedException.
     *
     * @return AssetsController A controller instance pre-wired to fail on client authorisation.
     * @throws Exception
     */
    protected function buildControllerWithUnauthorizedClient(): AssetsController
    {
        $androidRequest = new Request(server: [ 'HTTP_X_CLIENT_TYPE' => 'android' ]);

        $requestStack = $this->createMock(type: RequestStack::class);
        $requestStack->method(constraint: 'getCurrentRequest')
                     ->willReturn(value: $androidRequest);

        $guard = new RequestGuard(
            tokenStorage: $this->tokenStorage,
            requestStack: $requestStack,
        );

        $controller = new AssetsController(guard: $guard);
        $controller->setContainer(container: $this->container);

        return $controller;
    }
}
