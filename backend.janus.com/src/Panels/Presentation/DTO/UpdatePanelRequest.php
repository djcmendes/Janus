<?php

declare(strict_types=1);

namespace App\Panels\Presentation\DTO;

final class UpdatePanelRequest
{
    public string|null  $type      = '__UNCHANGED__';
    public string|null  $name      = '__UNCHANGED__';
    public string|null  $note      = '__UNCHANGED__';
    public mixed        $options   = '__UNCHANGED__';
    public int|string   $positionX = '__UNCHANGED__';
    public int|string   $positionY = '__UNCHANGED__';
    public int|string   $width     = '__UNCHANGED__';
    public int|string   $height    = '__UNCHANGED__';
}
