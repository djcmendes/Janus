<?php

declare(strict_types=1);

namespace App\Flows\Presentation\DTO;

final class UpdateOperationRequest
{
    public string|null $name        = '__UNCHANGED__';
    public string|null $type        = '__UNCHANGED__';
    public mixed       $options     = '__UNCHANGED__';
    public string|null $resolve     = '__UNCHANGED__';
    public string|null $nextSuccess = '__UNCHANGED__';
    public string|null $nextFailure = '__UNCHANGED__';
    public int|string  $sortOrder   = '__UNCHANGED__';
}
