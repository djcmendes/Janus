<?php

/**
 * @file ServerServiceTest.php
 *
 * Abstract base providing setUp / tearDown and shared helpers for all
 * ServerService test suites.
 *
 * Strategy: Connection is mocked so that database checks can be controlled
 * without a running database. Redis and RabbitMQ checks are exercised only
 * through the invalid-DSN paths (parse_url returns false) to avoid real
 * network calls in unit tests.
 *
 * @package App\Server\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Application\Service\Tests;

use App\Server\Application\Service\ServerService;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and helpers for all ServerService test suites.
 */
#[CoversClass(className: ServerService::class)]
abstract class ServerServiceTest extends TestCase
{
    /** Invalid DSN guaranteed to make parse_url() return false. */
    protected const string INVALID_DSN = ':';

    /** @var MockObject&Connection */
    protected MockObject $connection;

    /** @var ServerService */
    protected ServerService $class;

    public function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);

        $this->class = new ServerService(
            connection:   $this->connection,
            redisUrl:     self::INVALID_DSN,
            rabbitmqDsn:  self::INVALID_DSN,
        );
    }

    public function tearDown(): void
    {
        unset($this->connection, $this->class);
    }

    /**
     * Creates a ServerService whose database check is expected to return 'ok'.
     */
    protected function buildServiceWithHealthyDatabase(): ServerService
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('executeQuery');

        return new ServerService(
            connection:  $connection,
            redisUrl:    self::INVALID_DSN,
            rabbitmqDsn: self::INVALID_DSN,
        );
    }

    /**
     * Creates a ServerService whose database check throws a specific exception.
     */
    protected function buildServiceWithFailingDatabase(string $errorMessage): ServerService
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('executeQuery')->willThrowException(new \RuntimeException($errorMessage));

        return new ServerService(
            connection:  $connection,
            redisUrl:    self::INVALID_DSN,
            rabbitmqDsn: self::INVALID_DSN,
        );
    }
}
