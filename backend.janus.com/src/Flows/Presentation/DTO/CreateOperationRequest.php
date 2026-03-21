<?php

declare(strict_types=1);

namespace App\Flows\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateOperationRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 36)]
    public string $flowId = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    public string $type = '';

    public ?array  $options     = null;
    public ?string $resolve     = null;
    public ?string $nextSuccess = null;
    public ?string $nextFailure = null;
    public int     $sortOrder   = 0;
}
