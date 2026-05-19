<?php

/**
 * @file ServerServiceGetHealthTest.php
 *
 * Tests for ServerService::getHealth().
 *
 * @package App\Server\Application\Service\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Server\Application\Service\Tests;

use App\Server\Application\Service\ServerService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that getHealth() returns the expected structure and reflects
 * the status of each infrastructure dependency.
 */
#[CoversClass(className: ServerService::class)]
#[CoversMethod(ServerService::class, 'getHealth')]
final class ServerServiceGetHealthTest extends ServerServiceTest
{
    public function testGetHealthReturnsArray(): void
    {
        $this->assertIsArray($this->class->getHealth());
    }

    public function testGetHealthHasDatabaseKey(): void
    {
        $this->assertArrayHasKey('database', $this->class->getHealth());
    }

    public function testGetHealthHasRedisKey(): void
    {
        $this->assertArrayHasKey('redis', $this->class->getHealth());
    }

    public function testGetHealthHasRabbitmqKey(): void
    {
        $this->assertArrayHasKey('rabbitmq', $this->class->getHealth());
    }

    public function testGetHealthDatabaseOkWhenConnectionSucceeds(): void
    {
        $service = $this->buildServiceWithHealthyDatabase();

        $this->assertSame('ok', $service->getHealth()['database']);
    }

    public function testGetHealthDatabaseReturnsErrorMessageOnFailure(): void
    {
        $service = $this->buildServiceWithFailingDatabase('Connection refused');

        $this->assertSame('Connection refused', $service->getHealth()['database']);
    }

    public function testGetHealthRedisReturnsInvalidUrlWhenDsnIsUnparseable(): void
    {
        $this->assertSame('invalid REDIS_URL', $this->class->getHealth()['redis']);
    }

    public function testGetHealthRabbitmqReturnsInvalidDsnWhenDsnIsUnparseable(): void
    {
        $this->assertSame('invalid RABBITMQ_DSN', $this->class->getHealth()['rabbitmq']);
    }

    public function testGetHealthAllValuesAreStrings(): void
    {
        foreach ($this->class->getHealth() as $value) {
            $this->assertIsString($value);
        }
    }
}
