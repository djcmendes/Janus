<?php

/**
 * @file CommentMapperBaseTest.php
 *
 * Constructor and interface compliance tests for CommentMapper.
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Comments\Infrastructure\Persistence\Doctrine\Mapper\CommentMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that CommentMapper is correctly instantiated as a final class
 * with no dependencies.
 */
#[CoversClass(CommentMapper::class)]
final class CommentMapperBaseTest extends CommentMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of CommentMapper.
     */
    public function testIsInstanceOfCommentMapper(): void
    {
        $this->assertInstanceOf(CommentMapper::class, $this->class);
    }
}
