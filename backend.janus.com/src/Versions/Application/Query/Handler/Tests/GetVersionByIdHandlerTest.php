<?php

/**
 * @file GetVersionByIdHandlerTest.php
 *
 * Abstract base for all GetVersionByIdHandler test suites.
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\Query\Handler\GetVersionByIdHandler;
use App\Versions\Domain\Entity\Version;
use App\Versions\Domain\Repository\VersionRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Abstract base for GetVersionByIdHandler tests.
 */
#[CoversClass(className: GetVersionByIdHandler::class)]
abstract class GetVersionByIdHandlerTest extends TestCase
{
    /** @var string */
    protected const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * @var MockObject&VersionRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * @var GetVersionByIdHandler
     */
    protected GetVersionByIdHandler $class;

    /**
     * @var ReflectionClass<GetVersionByIdHandler>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(VersionRepositoryInterface::class);
        $this->class      = new GetVersionByIdHandler(repository: $this->repository);
        $this->reflection = new ReflectionClass(GetVersionByIdHandler::class);
    }

    protected function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }

    /**
     * @return Version
     */
    protected function makeVersion(): Version
    {
        return new Version('articles', 'item-uuid-1', 'main', ['title' => 'Hello']);
    }
}
