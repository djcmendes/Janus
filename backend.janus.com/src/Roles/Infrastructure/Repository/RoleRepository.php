<?php

declare(strict_types=1);

namespace App\Roles\Infrastructure\Repository;

use App\Roles\Domain\Entity\Role;
use App\Roles\Domain\Repository\RoleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
final class RoleRepository extends ServiceEntityRepository implements RoleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function save(Role $role, bool $flush = true): void
    {
        $this->getEntityManager()->persist($role);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Role $role): void
    {
        $this->getEntityManager()->remove($role);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Role
    {
        return $this->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->findOneBy(['name' => $name]);
    }

    /** @return Role[] */
    public function findAll(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
