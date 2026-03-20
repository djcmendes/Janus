<?php

declare(strict_types=1);

namespace App\Shares\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateShareRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $collection = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $item = '';

    public ?string $name      = null;
    public ?string $password  = null;
    public ?string $expiresAt = null;
    public ?int    $maxUses   = null;
}
