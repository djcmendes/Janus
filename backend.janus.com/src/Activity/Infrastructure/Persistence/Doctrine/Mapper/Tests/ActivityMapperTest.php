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
use DateTimeImmutable;
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
#[CoversClass(className:  ActivityMapper::class)]
abstract class ActivityMapperTest extends TestCase
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * The instance of the query being tested.
     * @var ActivityMapper
     */
    protected ActivityMapper $class;

    /**
     * Reflection of ActivityMapper class
     * @var ReflectionClass<ActivityMapper>
     */
    protected ReflectionClass $reflection;

    /**
     * TestCase Constructor.
     * Builds the SUT and its reflection mirror before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->class      = new ActivityMapper();
        $this->reflection = new ReflectionClass(objectOrClass: ActivityMapper::class);
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
     * @return ActivityEntity A hydrated Doctrine model ready for toDomain() tests.
     */
    protected function makeEntity(): ActivityEntity
    {
        return (new ActivityEntity())->setId(id: Uuid::fromString(uuid: self::FIXED_UUID))
                                     ->setAction(action: 'create')
                                     ->setCollection(collection: 'posts')
                                     ->setItem(item: '42')
                                     ->setUserId(userId: 'bbbbbbbb-0000-7000-8000-000000000002')
                                     ->setIp(ip: '127.0.0.1')
                                     ->setUserAgent(userAgent: 'PHPUnit')
                                     ->setTimestamp(timestamp: new DateTimeImmutable(datetime: '2024-01-01T00:00:00+00:00'));
    }

    /**
     * Creates a fully-populated domain Activity with deterministic test values.
     *
     * @return Activity A hydrated domain entity ready for toPersistence() tests.
     */
    protected function makeDomain(): Activity
    {
        $activity = new Activity(action: 'create', collection: 'posts', item: '42');
        $activity->setUserId(userId: 'bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp(ip: '127.0.0.1');
        $activity->setUserAgent(userAgent: 'PHPUnit');

        return $activity;
    }
}
