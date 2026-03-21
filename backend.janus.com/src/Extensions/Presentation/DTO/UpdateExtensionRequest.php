<?php

declare(strict_types=1);

namespace App\Extensions\Presentation\DTO;

final class UpdateExtensionRequest
{
    public bool|string  $enabled = '__UNCHANGED__';
    public string|null  $version = '__UNCHANGED__';
    public mixed        $meta    = '__UNCHANGED__';
}
