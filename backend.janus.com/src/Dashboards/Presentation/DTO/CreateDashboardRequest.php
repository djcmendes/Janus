<?php

declare(strict_types=1);

namespace App\Dashboards\Presentation\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateDashboardRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name = '';

    public ?string $icon = null;
    public ?string $note = null;
}
