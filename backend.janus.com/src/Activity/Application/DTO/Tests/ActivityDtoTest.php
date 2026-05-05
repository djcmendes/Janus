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
 * Abstract base for ActivityDto tests.
 *
 * Strategy: ActivityDto is a final class with no injectable dependencies.
 * Tests instantiate real Activity domain entities directly and pass them to
 * ActivityDto::fromEntity() to produce the SUT — no mocks are required.
 */
#[CoversClass(ActivityDto::class)]
abstract class ActivityDtoTest extends TestCase
{
    protected ActivityDto $class;

    /** @var ReflectionClass<ActivityDto> */
    protected ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->class      = ActivityDto::fromEntity($this->makeActivity());
        $this->reflection = new ReflectionClass(ActivityDto::class);
    }

    protected function tearDown(): void
    {
        unset($this->class, $this->reflection);
    }

    /**
     * Creates a fully-populated Activity entity with deterministic test values.
     *
     * @return Activity A hydrated entity ready for DTO conversion.
     */
    protected function makeActivity(): Activity
    {
        $activity = new Activity('create', 'posts', '42');
        $activity->setUserId('bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp('127.0.0.1');
        $activity->setUserAgent('PHPUnit');

        return $activity;
    }
}
