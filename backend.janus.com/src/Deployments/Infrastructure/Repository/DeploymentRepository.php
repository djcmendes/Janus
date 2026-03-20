<?php

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Repository\DeploymentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DeploymentRepository extends ServiceEntityRepository implements DeploymentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deployment::class);
    }

    public function save(Deployment $deployment, bool $flush = true): void
    {
        $this->getEntityManager()->persist($deployment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findById(string $id): ?Deployment
    {
        return $this->find($id);
    }

    public function findAll(int $limit, int $offset, ?string $providerId = null): array
    {
        $qb = $this->createQueryBuilder('d')->orderBy('d.startedAt', 'DESC');

        if ($providerId !== null) {
            $qb->andWhere('d.providerId = :pid')->setParameter('pid', $providerId);
        }

        return $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
    }

    public function countAll(?string $providerId = null): int
    {
        $qb = $this->createQueryBuilder('d')->select('COUNT(d.id)');

        if ($providerId !== null) {
            $qb->andWhere('d.providerId = :pid')->setParameter('pid', $providerId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
