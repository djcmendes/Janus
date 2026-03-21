<?php

declare(strict_types=1);

namespace App\Permissions\Infrastructure\Repository;

use App\Permissions\Domain\Entity\Permission;
use App\Permissions\Domain\Repository\PermissionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
final class PermissionRepository extends ServiceEntityRepository implements PermissionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function save(Permission $permission, bool $flush = true): void
    {
        $this->getEntityManager()->persist($permission);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Permission $permission): void
    {
        $this->getEntityManager()->remove($permission);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Permission
    {
        return $this->find($id);
    }

    /** @return Permission[] */
    public function findPaginated(int $limit, int $offset): array
    {
        return $this->findBy([], ['createdAt' => 'ASC'], $limit, $offset);
    }

    /** @return Permission[] */
    public function findByPolicy(string $policyId, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.policy', 'pol')
            ->where('pol.id = :policyId')
            ->setParameter('policyId', $policyId)
            ->orderBy('p.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
