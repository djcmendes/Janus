<?php

declare(strict_types=1);

namespace App\Fields\Application\DTO\Tests;

use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use PHPUnit\Framework\TestCase;

abstract class FieldDtoTest extends TestCase
{
    protected function makeFieldMeta(
        string             $id         = 'aaaaaaaa-0000-7000-8000-000000000001',
        string             $collection = 'articles',
        string             $field      = 'title',
        FieldType          $type       = FieldType::STRING,
        ?string            $label      = 'Article Title',
        ?string            $note       = null,
        bool               $required   = false,
        bool               $readonly   = false,
        bool               $hidden     = false,
        int                $sortOrder  = 0,
        ?string            $interface  = null,
        ?array             $options    = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): FieldMeta {
        return FieldMeta::reconstitute(
            id:         $id,
            collection: $collection,
            field:      $field,
            type:       $type,
            label:      $label,
            note:       $note,
            required:   $required,
            readonly:   $readonly,
            hidden:     $hidden,
            sortOrder:  $sortOrder,
            interface:  $interface,
            options:    $options,
            createdAt:  new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt:  $updatedAt,
        );
    }
}
