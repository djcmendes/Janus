<?php

/**
 * @file CommentEntityTest.php
 *
 * Abstract base for all CommentEntity test suites.
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for CommentEntity tests.
 *
 * Strategy: CommentEntity has no injectable dependencies. Tests instantiate
 * it directly — no mocking is required. The class is non-final (required for
 * Doctrine proxy generation), so a real instance is used as the SUT.
 */
#[CoversClass(CommentEntity::class)]
abstract class CommentEntityTest extends TestCase
{
    /**
     * Instance of the class being tested.
     * @var CommentEntity
     */
    protected CommentEntity $class;

    /**
     * Reflection of CommentEntity.
     * @var ReflectionClass<CommentEntity>
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
        $this->class      = new CommentEntity();
        $this->reflection = new ReflectionClass(CommentEntity::class);
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
     * Creates a fully-populated CommentEntity with deterministic test values.
     *
     * @return CommentEntity A hydrated entity ready for assertion.
     */
    protected function makeEntity(): CommentEntity
    {
        return (new CommentEntity())
            ->setId(Uuid::fromString('aaaaaaaa-0000-7000-8000-000000000001'))
            ->setCollection('posts')
            ->setItem('42')
            ->setComment('Hello world')
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }
}
