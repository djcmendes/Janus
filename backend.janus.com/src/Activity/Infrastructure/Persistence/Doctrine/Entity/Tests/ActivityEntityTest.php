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

    protected function setUp(): void
    {
        $this->class      = new ActivityEntity();
        $this->reflection = new ReflectionClass(ActivityEntity::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated ActivityEntity with deterministic test values.
     *
     * @return ActivityEntity A hydrated entity ready for assertion.
     */
    protected function makeEntity(): ActivityEntity
    {
        return (new ActivityEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setAction('create')
            ->setCollection('posts')
            ->setItem('42')
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setIp('127.0.0.1')
            ->setUserAgent('PHPUnit')
            ->setTimestamp(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
    }
}
