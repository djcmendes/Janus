<?php

declare(strict_types=1);

namespace App\Users\Application\DTO;

use App\Users\Domain\Entity\User;

final class UserDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $email,
        public readonly string  $status,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly array   $roles,
        public readonly ?string $roleId,
        public readonly ?string $inviteToken,
        public readonly ?string $lastAccessAt,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id:           (string) $user->getId(),
            email:        $user->getEmail(),
            status:       $user->getStatus(),
            firstName:    $user->getFirstName(),
            lastName:     $user->getLastName(),
            roles:        $user->getRoles(),
            roleId:       $user->getRole() ? (string) $user->getRole()->getId() : null,
            inviteToken:  $user->getInviteToken(),
            lastAccessAt: $user->getLastAccessAt()?->format(\DateTimeInterface::ATOM),
            createdAt:    $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:    $user->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'email'          => $this->email,
            'status'         => $this->status,
            'first_name'     => $this->firstName,
            'last_name'      => $this->lastName,
            'roles'          => $this->roles,
            'role'           => $this->roleId,
            'last_access_at' => $this->lastAccessAt,
            'created_at'     => $this->createdAt,
            'updated_at'     => $this->updatedAt,
        ];
    }
}
