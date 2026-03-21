<?php

declare(strict_types=1);

namespace App\Presets\Presentation\DTO;

final class UpdatePresetRequest
{
    public string|null $collection    = '__UNCHANGED__';
    public string|null $layout        = '__UNCHANGED__';
    public mixed       $layoutOptions = '__UNCHANGED__';
    public mixed       $layoutQuery   = '__UNCHANGED__';
    public mixed       $filter        = '__UNCHANGED__';
    public string|null $search        = '__UNCHANGED__';
    public string|null $bookmark      = '__UNCHANGED__';
}
