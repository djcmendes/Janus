<?php

/**
 * @file ActivityEntityTest.php
 *
 * Abstract base for all ActivityEntity test suites.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for ActivityEntity tests.
 *
 * Strategy: ActivityEntity has no injectable dependencies. Tests instantiate
 * it directly — no mocking is required. The class is non-final (required for
 * Doctrine proxy generation), so a real instance is used as the SUT.
 */
#[CoversClass(ActivityEntity::class)]
abstract class ActivityEntityTest extends TestCase
{
    protected ActivityEntity $class;

    /** @var ReflectionClass<ActivityEntity> */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new ActivityEntity();
        $this->reflection = new ReflectionClass(objectOrClass: ActivityEntity::class);
    }

    /**
     * TestCase Destructor.
     * Releases SUT references after each test to prevent state bleed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset(
            $this->class,
            $this->reflection
        );
    }

    /**
     * Creates a fully-populated ActivityEntity with deterministic test values.
     *
     * @return ActivityEntity A hydrated entity ready for assertion.
     */
    protected function makeEntity(): ActivityEntity
    {
        return (new ActivityEntity())
            ->setId(id: Uuid::fromString(uuid: 'aaaaaaaa-0000-7000-8000-000000000001'))
            ->setAction(action: 'create')
            ->setCollection(collection: 'posts')
            ->setItem(item: '42')
            ->setUserId(userId: 'bbbbbbbb-0000-7000-8000-000000000002')
            ->setIp(ip: '127.0.0.1')
            ->setUserAgent(userAgent: 'PHPUnit')
            ->setTimestamp(timestamp: new DateTimeImmutable(datetime: '2024-01-01T00:00:00+00:00'));
    }
}
