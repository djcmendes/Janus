<?php

declare(strict_types=1);

namespace App\Translations\Infrastructure\Repository;

use App\Translations\Domain\Entity\Translation;
use App\Translations\Domain\Repository\TranslationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Translation>
 */
final class TranslationRepository extends ServiceEntityRepository implements TranslationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    public function save(Translation $translation): void
    {
        $this->getEntityManager()->persist($translation);
        $this->getEntityManager()->flush();
    }

    public function delete(Translation $translation): void
    {
        $this->getEntityManager()->remove($translation);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Translation
    {
        return $this->find($id);
    }

    public function findByLanguageAndKey(string $language, string $key): ?Translation
    {
        return $this->findOneBy(['language' => $language, 'key' => $key]);
    }

    /** @return Translation[] */
    public function findAll(
        int     $limit,
        int     $offset,
        ?string $language = null,
        ?string $key      = null,
    ): array {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.language', 'ASC')
            ->addOrderBy('t.key', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($language !== null) {
            $qb->andWhere('t.language = :language')->setParameter('language', $language);
        }
        if ($key !== null) {
            $qb->andWhere('t.key LIKE :key')->setParameter('key', $key . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function countAll(
        ?string $language = null,
        ?string $key      = null,
    ): int {
        $qb = $this->createQueryBuilder('t')->select('COUNT(t.id)');

        if ($language !== null) {
            $qb->andWhere('t.language = :language')->setParameter('language', $language);
        }
        if ($key !== null) {
            $qb->andWhere('t.key LIKE :key')->setParameter('key', $key . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
