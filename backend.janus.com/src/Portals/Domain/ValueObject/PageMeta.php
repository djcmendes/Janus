<?php
declare(strict_types=1);
namespace App\Portals\Domain\ValueObject;
final class PageMeta
{
    public function __construct(
        public readonly ?string $metaTitle       = null,
        public readonly ?string $metaDescription = null,
        public readonly ?string $ogImage         = null,
        public readonly bool    $noIndex         = false,
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            metaTitle:       $data['meta_title']       ?? null,
            metaDescription: $data['meta_description'] ?? null,
            ogImage:         $data['og_image']         ?? null,
            noIndex:         (bool) ($data['no_index'] ?? false),
        );
    }
    public function toArray(): array
    {
        return [
            'meta_title'       => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'og_image'         => $this->ogImage,
            'no_index'         => $this->noIndex,
        ];
    }
}
