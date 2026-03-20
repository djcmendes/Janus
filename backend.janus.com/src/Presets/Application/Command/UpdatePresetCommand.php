<?php

declare(strict_types=1);

namespace App\Presets\Application\Command;

final class UpdatePresetCommand
{
    public const UNCHANGED = '__UNCHANGED__';

    public function __construct(
        public readonly string       $id,
        public readonly string|null  $collection,
        public readonly string|null  $layout,
        /** @var array|string|null — UNCHANGED sentinel or actual value */
        public readonly mixed        $layoutOptions,
        /** @var array|string|null — UNCHANGED sentinel or actual value */
        public readonly mixed        $layoutQuery,
        /** @var array|string|null — UNCHANGED sentinel or actual value */
        public readonly mixed        $filter,
        public readonly string|null  $search,
        public readonly string|null  $bookmark,
        public readonly string       $requestingUserId,
        public readonly bool         $isAdmin = false,
    ) {}
}
