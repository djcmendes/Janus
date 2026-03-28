<?php
declare(strict_types=1);
namespace App\Portals\Infrastructure\Repository;

use App\Portals\Domain\Entity\AclRule;
use App\Portals\Domain\Repository\AclRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AclRule> */
final class AclRepository extends ServiceEntityRepository implements AclRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AclRule::class);
    }

    public function save(AclRule $rule, bool $flush = true): void
    {
        $this->getEntityManager()->persist($rule);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function delete(AclRule $rule): void
    {
        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?AclRule { return $this->find($id); }

    /** @return AclRule[] */
    public function findBySubject(string $subjectType, string $subjectId): array
    {
        return $this->findBy(['subjectType' => $subjectType, 'subjectId' => $subjectId]);
    }

    /** @return AclRule[] */
    public function findByRole(string $roleId): array
    {
        return $this->findBy(['roleId' => $roleId]);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function deleteBySubject(string $subjectType, string $subjectId): void
    {
        $this->createQueryBuilder('a')
            ->delete()
            ->where('a.subjectType = :type')
            ->andWhere('a.subjectId = :id')
            ->setParameter('type', $subjectType)
            ->setParameter('id', $subjectId)
            ->getQuery()
            ->execute();
    }
}
