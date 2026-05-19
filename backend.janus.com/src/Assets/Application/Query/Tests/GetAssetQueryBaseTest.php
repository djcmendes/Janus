<?php

/**
 * @file GetAssetQueryBaseTest.php
 *
 * Constructor and property compliance tests for GetAssetQuery.
 *
 * @package App\Assets\Application\Query\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query\Tests;

use App\Assets\Application\Query\GetAssetQuery;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: GetAssetQuery::class)]
final class GetAssetQueryBaseTest extends GetAssetQueryTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of GetAssetQuery.
     */
    public function testIsInstanceOfGetAssetQuery(): void
    {
        $this->assertInstanceOf(GetAssetQuery::class, $this->class);
    }

    /**
     * Test that the constructor stores the UUID string on the id property.
     */
    public function testConstructorStoresId(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->id);
    }

    /**
     * Test that the constructor stores the fit mode string on the fit property.
     */
    public function testConstructorStoresFit(): void
    {
        $this->assertSame('contain', $this->class->fit);
    }

    /**
     * Test that the constructor stores the format string on the format property.
     */
    public function testConstructorStoresFormat(): void
    {
        $this->assertSame('jpg', $this->class->format);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that width and height default to null when not provided.
     */
    public function testWidthAndHeightDefaultToNull(): void
    {
        $this->assertNull($this->class->width);
        $this->assertNull($this->class->height);
    }

    /**
     * Test that the constructor stores a provided width value on the width property.
     */
    public function testConstructorStoresWidthWhenProvided(): void
    {
        $query = new GetAssetQuery('aaaaaaaa-0000-7000-8000-000000000001', 1920, null, 'contain', 'jpg');

        $this->assertSame(1920, $query->width);
    }

    /**
     * Test that the constructor stores a provided height value on the height property.
     */
    public function testConstructorStoresHeightWhenProvided(): void
    {
        $query = new GetAssetQuery('aaaaaaaa-0000-7000-8000-000000000001', null, 1080, 'contain', 'jpg');

        $this->assertSame(1080, $query->height);
    }

    /**
     * Test that the constructor stores all five fields correctly when fully populated.
     */
    public function testConstructorStoresAllFieldsWhenFullyPopulated(): void
    {
        $query = new GetAssetQuery('bbbbbbbb-0000-7000-8000-000000000002', 800, 600, 'cover', 'png');

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $query->id);
        $this->assertSame(800, $query->width);
        $this->assertSame(600, $query->height);
        $this->assertSame('cover', $query->fit);
        $this->assertSame('png', $query->format);
    }

    /**
     * Test that all five public properties are declared readonly.
     */
    public function testAllPropertiesAreReadonly(): void
    {
        foreach (['id', 'width', 'height', 'fit', 'format'] as $property) {
            $this->assertTrue(
                $this->reflection->getProperty($property)->isReadOnly(),
                "Property \${$property} must be readonly.",
            );
        }
    }
}
