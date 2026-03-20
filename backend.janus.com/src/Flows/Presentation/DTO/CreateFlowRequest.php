<?php

declare(strict_types=1);

namespace App\Flows\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateFlowRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['active', 'inactive'])]
    public string $status = 'inactive';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['manual', 'action', 'schedule', 'webhook'])]
    public string $trigger = 'manual';

    public ?array  $triggerOptions = null;
    public ?string $description    = null;
}
