<?php

/**
 * @file ActivityMapperTest.php
 *
 * Abstract base for all ActivityMapper test suites.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Activity\Domain\Entity\Activity;
use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use App\Activity\Infrastructure\Persistence\Doctrine\Mapper\ActivityMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for ActivityMapper tests.
 *
 * Strategy: ActivityMapper, Activity, and ActivityEntity are all instantiated
 * as real objects. All three classes are pure with no injectable dependencies,
 * so no mocking is required.
 */
#[CoversClass(ActivityMapper::class)]
abstract class ActivityMapperTest extends TestCase
{
    /** @var string */
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    protected ActivityMapper $class;

    /** @var ReflectionClass<ActivityMapper> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = new ActivityMapper();
        $this->reflection = new ReflectionClass(ActivityMapper::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated ActivityEntity with deterministic test values.
     *
     * @return ActivityEntity A hydrated Doctrine model ready for toDomain() tests.
     */
    protected function makeEntity(): ActivityEntity
    {
        return (new ActivityEntity())
            ->setId(Uuid::fromString(self::FIXED_UUID))
            ->setAction('create')
            ->setCollection('posts')
            ->setItem('42')
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setIp('127.0.0.1')
            ->setUserAgent('PHPUnit')
            ->setTimestamp(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'));
    }

    /**
     * Creates a fully-populated domain Activity with deterministic test values.
     *
     * @return Activity A hydrated domain entity ready for toPersistence() tests.
     */
    protected function makeDomain(): Activity
    {
        $activity = new Activity('create', 'posts', '42');
        $activity->setUserId('bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp('127.0.0.1');
        $activity->setUserAgent('PHPUnit');

        return $activity;
    }
}
