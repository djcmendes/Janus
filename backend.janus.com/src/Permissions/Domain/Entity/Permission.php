<?php

declare(strict_types=1);

namespace App\Permissions\Domain\Entity;

use App\Permissions\Domain\Enum\PermissionAction;
use App\Policies\Domain\Entity\Policy;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'permissions')]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Policy::class)]
    #[ORM\JoinColumn(name: 'policy_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Policy $policy;

    /** Null means the rule applies to all collections. */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $collection = null;

    #[ORM\Column(length: 20, enumType: PermissionAction::class)]
    private PermissionAction $action;

    /** Allowed field names. Null means all fields are allowed. */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $fields = null;

    /** Row-level filter (e.g. {"user": {"_eq": "$CURRENT_USER"}}). Null = no filter. */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $permissionsFilter = null;

    /** Validation rules applied on write operations. Null = no validation. */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $validation = null;

    /** Default field presets applied on create/update. Null = no presets. */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $presets = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(Policy $policy, PermissionAction $action)
    {
        $this->id        = Uuid::v7();
        $this->policy    = $policy;
        $this->action    = $action;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getPolicy(): Policy { return $this->policy; }

    public function getCollection(): ?string { return $this->collection; }
    public function setCollection(?string $v): static { $this->collection = $v; return $this->touch(); }

    public function getAction(): PermissionAction { return $this->action; }
    public function setAction(PermissionAction $v): static { $this->action = $v; return $this->touch(); }

    public function getFields(): ?array { return $this->fields; }
    public function setFields(?array $v): static { $this->fields = $v; return $this->touch(); }

    public function getPermissionsFilter(): ?array { return $this->permissionsFilter; }
    public function setPermissionsFilter(?array $v): static { $this->permissionsFilter = $v; return $this->touch(); }

    public function getValidation(): ?array { return $this->validation; }
    public function setValidation(?array $v): static { $this->validation = $v; return $this->touch(); }

    public function getPresets(): ?array { return $this->presets; }
    public function setPresets(?array $v): static { $this->presets = $v; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
