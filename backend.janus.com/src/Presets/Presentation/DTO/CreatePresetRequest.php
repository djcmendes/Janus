<?php

declare(strict_types=1);

namespace App\Presets\Presentation\DTO;

final class CreatePresetRequest
{
    public ?string $collection    = null;
    public ?string $layout        = null;
    public ?array  $layoutOptions = null;
    public ?array  $layoutQuery   = null;
    public ?array  $filter        = null;
    public ?string $search        = null;
    public ?string $bookmark      = null;
}
