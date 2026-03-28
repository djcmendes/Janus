<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use App\Portals\Domain\ValueObject\Position;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
#[ORM\Entity]
#[ORM\Table(name: 'layout_templates')]
final class LayoutTemplate
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(length: 255)]
    private string $name;
    #[ORM\Column(name: 'positions_json', type: 'json')]
    private array $positionsJson;
    #[ORM\Column(name: 'template_markup', type: 'text')]
    private string $templateMarkup;
    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    public function __construct(string $name, array $positions, string $templateMarkup)
    {
        $this->id             = (string) Uuid::v7();
        $this->name           = $name;
        $this->positionsJson  = array_map(fn (Position $p) => $p->toArray(), $positions);
        $this->templateMarkup = $templateMarkup;
        $this->createdAt      = new \DateTimeImmutable();
    }
    public function update(string $name, array $positions, string $templateMarkup): void
    {
        $this->name           = $name;
        $this->positionsJson  = array_map(fn (Position $p) => $p->toArray(), $positions);
        $this->templateMarkup = $templateMarkup;
        $this->touch();
    }
    public function getId(): string                   { return $this->id; }
    public function getName(): string                 { return $this->name; }
    /** @return Position[] */
    public function getPositions(): array             { return array_map(fn ($p) => Position::fromArray($p), $this->positionsJson); }
    public function getTemplateMarkup(): string       { return $this->templateMarkup; }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
