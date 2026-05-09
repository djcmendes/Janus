<?php

/**
 * @file CommentMapperTest.php
 *
 * Abstract base for all CommentMapper test suites.
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * Abstract base for CommentMapper tests.
 *
 * Strategy: CommentMapper, Comment, and CommentEntity are all instantiated
 * as real objects. All three classes are pure with no injectable dependencies,
 * so no mocking is required.
 */
#[CoversClass(CommentMapper::class)]
abstract class CommentMapperTest extends TestCase
{
    /** @var string */
    protected const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Instance of the class being tested.
     * @var CommentMapper
     */
    protected CommentMapper $class;

    /**
     * Reflection of CommentMapper.
     * @var ReflectionClass<CommentMapper>
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
        $this->class      = new CommentMapper();
        $this->reflection = new ReflectionClass(CommentMapper::class);
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
     * @return CommentEntity A hydrated Doctrine model ready for toDomain() tests.
     */
    protected function makeEntity(): CommentEntity
    {
        return (new CommentEntity())
            ->setId(Uuid::fromString(self::FIXED_UUID))
            ->setCollection('posts')
            ->setItem('42')
            ->setComment('Hello world')
            ->setUserId('bbbbbbbb-0000-7000-8000-000000000002')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00+00:00'))
            ->setUpdatedAt(null);
    }

    /**
     * Creates a fully-populated domain Comment with deterministic test values.
     *
     * @return Comment A hydrated domain entity ready for toPersistence() tests.
     */
    protected function makeDomain(): Comment
    {
        return new Comment('posts', '42', 'Hello world', 'bbbbbbbb-0000-7000-8000-000000000002');
    }
}
