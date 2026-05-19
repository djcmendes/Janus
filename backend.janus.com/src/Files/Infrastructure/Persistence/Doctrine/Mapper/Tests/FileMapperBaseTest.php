<?php

/**
 * @file FileMapperBaseTest.php
 *
 * Constructor and interface compliance tests for FileMapper.
 *
 * @package App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Files\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Files\Infrastructure\Persistence\Doctrine\Mapper\FileMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: FileMapper::class)]
final class FileMapperBaseTest extends FileMapperTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfFileMapper(): void
    {
        $this->assertInstanceOf(FileMapper::class, $this->class);
    }
}
