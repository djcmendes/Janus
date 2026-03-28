<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;
use App\Portals\Domain\ValueObject\PortalId;
use App\Portals\Domain\ValueObject\PortalSettings;
use App\Portals\Domain\ValueObject\PortalStatus;
use App\Portals\Domain\ValueObject\Route;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'portals')]
final class Portal
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(name: 'base_route', length: 255)]
    private string $baseRoute;

    #[ORM\Column(length: 50)]
    private string $status;

    #[ORM\Column(name: 'settings_json', type: 'json', nullable: true)]
    private ?array $settingsJson = null;

    #[ORM\Column(name: 'created_at')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    private function __construct(
        PortalId      $id,
        string        $name,
        Route         $baseRoute,
        PortalStatus  $status,
        PortalSettings $settings,
    ) {
        $this->id          = $id->toString();
        $this->name        = $name;
        $this->baseRoute   = $baseRoute->toString();
        $this->status      = $status->value;
        $this->settingsJson = $settings->toArray();
        $this->createdAt   = new \DateTimeImmutable();
    }

    public static function create(string $name, Route $baseRoute, PortalStatus $status = PortalStatus::DRAFT, PortalSettings $settings = new PortalSettings()): self
    {
        return new self(PortalId::generate(), $name, $baseRoute, $status, $settings);
    }

    public function updateSettings(PortalSettings $settings): void
    {
        $this->settingsJson = $settings->toArray();
        $this->touch();
    }

    public function rename(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function changeBaseRoute(Route $route): void
    {
        $this->baseRoute = $route->toString();
        $this->touch();
    }

    public function changeStatus(PortalStatus $status): void
    {
        $this->status = $status->value;
        $this->touch();
    }

    public function archive(): void
    {
        $this->changeStatus(PortalStatus::ARCHIVED);
    }

    public function getId(): PortalId          { return PortalId::fromString($this->id); }
    public function getName(): string           { return $this->name; }
    public function getBaseRoute(): Route       { return new Route($this->baseRoute); }
    public function getStatus(): PortalStatus   { return PortalStatus::from($this->status); }
    public function getSettings(): PortalSettings { return PortalSettings::fromArray($this->settingsJson ?? []); }
    public function getCreatedAt(): \DateTimeImmutable  { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
}
