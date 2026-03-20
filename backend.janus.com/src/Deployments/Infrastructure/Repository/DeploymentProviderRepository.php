<?php

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DeploymentProviderRepository extends ServiceEntityRepository implements DeploymentProviderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeploymentProvider::class);
    }

    public function save(DeploymentProvider $provider, bool $flush = true): void
    {
        $this->getEntityManager()->persist($provider);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(DeploymentProvider $provider): void
    {
        $this->getEntityManager()->remove($provider);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?DeploymentProvider
    {
        return $this->find($id);
    }

    public function findAll(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countAll(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
