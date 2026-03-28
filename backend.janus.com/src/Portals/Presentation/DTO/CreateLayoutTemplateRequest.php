<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;
final class CreateLayoutTemplateRequest
{
    public function __construct(
        public readonly string $name,
        public readonly array  $positions,
        public readonly string $templateMarkup,
    ) {}
    public static function fromArray(array $data): self
    {
        if (empty($data['name']))            { throw new \InvalidArgumentException('name is required.'); }
        if (!isset($data['template_markup'])) { throw new \InvalidArgumentException('template_markup is required.'); }
        return new self(
            name:           trim($data['name']),
            positions:      $data['positions']       ?? [],
            templateMarkup: $data['template_markup'],
        );
    }
}
