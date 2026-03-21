<?php

declare(strict_types=1);

namespace App\Panels\Application\Command;

final class UpdatePanelCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string      $id,
        public readonly string|null $type,
        public readonly string|null $name,
        public readonly string|null $note,
        /** @var array|string|null — UNCHANGED sentinel or actual value */
        public readonly mixed       $options,
        public readonly int|string  $positionX,
        public readonly int|string  $positionY,
        public readonly int|string  $width,
        public readonly int|string  $height,
    ) {}
}
