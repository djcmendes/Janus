<?php

declare(strict_types=1);

namespace App\Extensions\Application\Command;

final class RegisterExtensionCommand
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $version,
        public readonly bool    $enabled     = false,
        public readonly ?string $description = null,
        public readonly ?array  $meta        = null,
    ) {}
}
