<?php

declare(strict_types=1);

namespace App\Panels\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreatePanelRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 36)]
    public string $dashboardId = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $type = '';

    public ?string $name      = null;
    public ?string $note      = null;
    public ?array  $options   = null;
    public int     $positionX = 0;
    public int     $positionY = 0;
    public int     $width     = 6;
    public int     $height    = 4;
}
