<?php

declare(strict_types=1);

namespace App\Flows\Presentation\DTO;

final class UpdateFlowRequest
{
    public string|null $name           = '__UNCHANGED__';
    public string|null $status         = '__UNCHANGED__';
    public string|null $trigger        = '__UNCHANGED__';
    public mixed       $triggerOptions = '__UNCHANGED__';
    public string|null $description    = '__UNCHANGED__';
}
