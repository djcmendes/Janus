<?php

/**
 * @file VersionServiceTest.php
 *
 * Abstract base for all VersionService test suites.
 *
 * Strategy: VersionService is final, so it is instantiated as a real object
 * backed by a mocked Doctrine DBAL Connection.
 *
 * @package App\Versions\Infrastructure\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Service\Tests;

use App\Versions\Domain\Entity\Version;
use App\Versions\Infrastructure\Service\VersionService;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and factory helpers for all VersionService test suites.
 */
#[CoversClass(className: VersionService::class)]
abstract class VersionServiceTest extends TestCase
{
    /**
     * Mock of the DBAL connection — controls executeStatement behaviour.
     * @var MockObject&Connection
     */
    protected MockObject $connection;

    /**
     * The system under test — real VersionService backed by a mocked connection.
     * @var VersionService
     */
    protected VersionService $class;

    /**
     * @var ReflectionClass<VersionService>
     */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->class      = new VersionService(connection: $this->connection);
        $this->reflection = new ReflectionClass(VersionService::class);
    }

    protected function tearDown(): void
    {
        unset($this->connection, $this->class, $this->reflection);
    }

    /**
     * Creates a Version entity ready for promotion tests.
     *
     * @param array<string, mixed> $data Data snapshot the version will carry.
     *
     * @return Version A domain entity with collection, item, and data set.
     */
    protected function makeVersion(array $data = ['title' => 'Hello', 'body' => 'World']): Version
    {
        return new Version(
            collection: 'articles',
            item:       'item-uuid-1',
            key:        'main',
            data:       $data,
        );
    }
}
