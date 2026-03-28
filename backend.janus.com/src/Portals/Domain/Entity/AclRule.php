<?php
declare(strict_types=1);
namespace App\Portals\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'acl_rules')]
final class AclRule
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'subject_type', length: 50)]
    private string $subjectType;

    #[ORM\Column(name: 'subject_id', length: 255)]
    private string $subjectId;

    #[ORM\Column(name: 'role_id', type: 'string', length: 36)]
    private string $roleId;

    #[ORM\Column(length: 50)]
    private string $permission;

    public function __construct(
        string $subjectType,
        string $subjectId,
        string $roleId,
        string $permission,
    ) {
        $this->id          = Uuid::v7()->toRfc4122();
        $this->subjectType = $subjectType;
        $this->subjectId   = $subjectId;
        $this->roleId      = $roleId;
        $this->permission  = $permission;
    }

    public function getId(): string          { return $this->id; }
    public function getSubjectType(): string { return $this->subjectType; }
    public function getSubjectId(): string   { return $this->subjectId; }
    public function getRoleId(): string      { return $this->roleId; }
    public function getPermission(): string  { return $this->permission; }
}
