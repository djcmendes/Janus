<?php

declare(strict_types=1);

namespace App\Policies\Domain\Entity;

use App\Roles\Domain\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Access is the junction between a Role and a Policy.
 * A null role means the policy applies to the public (unauthenticated) access level.
 */
#[ORM\Entity]
#[ORM\Table(name: 'access')]
#[ORM\UniqueConstraint(name: 'UNIQ_ACCESS_ROLE_POLICY', columns: ['role_id', 'policy_id'])]
class Access
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: Policy::class)]
    #[ORM\JoinColumn(name: 'policy_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Policy $policy;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(?Role $role, Policy $policy)
    {
        $this->id        = Uuid::v7();
        $this->role      = $role;
        $this->policy    = $policy;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getRole(): ?Role { return $this->role; }
    public function getPolicy(): Policy { return $this->policy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
