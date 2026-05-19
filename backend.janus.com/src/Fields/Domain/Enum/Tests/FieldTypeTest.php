<?php

declare(strict_types=1);

namespace App\Fields\Domain\Enum\Tests;

use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(className: FieldType::class)]
final class FieldTypeTest extends TestCase
{
    public function testAllCasesHaveStringValues(): void
    {
        foreach (FieldType::cases() as $case) {
            $this->assertIsString($case->value);
            $this->assertNotEmpty($case->value);
        }
    }

    public function testAliasIsAlias(): void
    {
        $this->assertTrue(FieldType::ALIAS->isAlias());
    }

    public function testStringIsNotAlias(): void
    {
        $this->assertFalse(FieldType::STRING->isAlias());
    }

    public function testTextIsNotAlias(): void
    {
        $this->assertFalse(FieldType::TEXT->isAlias());
    }

    public function testAliasToColumnDdlReturnsEmptyString(): void
    {
        $this->assertSame('', FieldType::ALIAS->toColumnDdl());
    }

    public function testStringToColumnDdlContainsVarchar(): void
    {
        $this->assertStringContainsStringIgnoringCase('VARCHAR', FieldType::STRING->toColumnDdl());
    }

    public function testTextToColumnDdlContainsText(): void
    {
        $this->assertStringContainsStringIgnoringCase('TEXT', FieldType::TEXT->toColumnDdl());
    }

    public function testIntegerToColumnDdlContainsInt(): void
    {
        $this->assertStringContainsStringIgnoringCase('INT', FieldType::INTEGER->toColumnDdl());
    }

    public function testBigIntToColumnDdlContainsBigint(): void
    {
        $this->assertStringContainsStringIgnoringCase('BIGINT', FieldType::BIG_INT->toColumnDdl());
    }

    public function testBooleanToColumnDdlContainsTinyint(): void
    {
        $this->assertStringContainsStringIgnoringCase('TINYINT', FieldType::BOOLEAN->toColumnDdl());
    }

    public function testJsonToColumnDdlContainsJson(): void
    {
        $this->assertStringContainsStringIgnoringCase('JSON', FieldType::JSON->toColumnDdl());
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(FieldType::STRING, FieldType::tryFrom('string'));
    }

    public function testTryFromWithInvalidValueReturnsNull(): void
    {
        $this->assertNull(FieldType::tryFrom('nonexistent'));
    }

    public function testFromStringValue(): void
    {
        $this->assertSame(FieldType::STRING, FieldType::from('string'));
    }
}
