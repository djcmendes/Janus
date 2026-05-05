<?php

/**
 * @file GetActivityHandlerTest.php
 *
 * Abstract base providing setUp / tearDown, shared real instances, and
 * shared mock properties for all GetActivityHandler test cases.
 *
 * Strategy: GetActivityHandler is declared `final readonly` and cannot be
 * mocked directly. It is instantiated as a real object. Its only dependency,
 * ActivityRepositoryInterface, is an interface and is mocked normally.
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\Query\Handler\GetActivityHandler;
use App\Activity\Domain\Entity\Activity;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Common setup, teardown, and shared mock infrastructure for all
 * GetActivityHandler test suites.
 */
#[CoversClass(GetActivityHandler::class)]
abstract class GetActivityHandlerTest extends TestCase
{
    /**
     * Mock of the domain repository interface.
     * @var MockObject&ActivityRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test — real GetActivityHandler backed by a mocked repository.
     * @var GetActivityHandler
     */
    protected GetActivityHandler $class;

    /**
     * Reflection of GetActivityHandler for reading private properties.
     * @var ReflectionClass<GetActivityHandler>
     */
    protected ReflectionClass $reflection;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ActivityRepositoryInterface::class);
        $this->class      = new GetActivityHandler($this->repository);
        $this->reflection = new ReflectionClass(GetActivityHandler::class);
    }

    public function tearDown(): void
    {
        unset($this->repository, $this->class, $this->reflection);
    }

    // ── Entity factory ────────────────────────────────────────────────────────

    /**
     * Creates a fully-populated Activity entity for use in test assertions.
     *
     * @param string      $action     The action type.
     * @param string|null $collection The collection name, or null.
     * @param string|null $item       The item identifier, or null.
     *
     * @return Activity A hydrated entity with deterministic test metadata.
     */
    protected function makeActivity(
        string  $action     = 'create',
        ?string $collection = 'posts',
        ?string $item       = '1',
    ): Activity {
        $activity = new Activity($action, $collection, $item);
        $activity->setUserId('bbbbbbbb-0000-7000-8000-000000000002');
        $activity->setIp('127.0.0.1');
        $activity->setUserAgent('PHPUnit');

        return $activity;
    }
}
