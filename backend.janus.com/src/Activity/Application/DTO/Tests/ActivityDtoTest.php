<?php

/**
 * @file ActivityDtoTest.php
 *
 * Abstract base for all ActivityDto test suites.
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the ActivityDto class.
 */
#[CoversClass(ActivityDto::class)]
abstract class ActivityDtoTest extends TestCase
{
    /**
     * The DTO instance under test, built from a real Activity entity.
     * @var ActivityDto
     */
    protected ActivityDto $class;

    /**
     * Reflection of ActivityDto class
     * @var ReflectionClass<ActivityDto>
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
        $this->class      = ActivityDto::fromEntity(a: $this->makeActivity());
        $this->reflection = new ReflectionClass(objectOrClass: ActivityDto::class);
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
     * Creates a fully-populated Activity entity with deterministic test values.
     *
     * @return Activity A hydrated entity ready for DTO conversion.
     */
    protected function makeActivity(): Activity
    {
        $activity = new Activity(action: 'create', collection: 'posts', item: '42');
        $activity->setUserId(v: 'bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp(v: '127.0.0.1');
        $activity->setUserAgent(v: 'PHPUnit');

        return $activity;
    }
}
