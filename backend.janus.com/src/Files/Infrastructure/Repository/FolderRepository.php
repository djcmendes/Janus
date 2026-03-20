<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Repository;

use App\Files\Domain\Entity\Folder;
use App\Files\Domain\Repository\FolderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Folder>
 */
final class FolderRepository extends ServiceEntityRepository implements FolderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    public function save(Folder $folder, bool $flush = true): void
    {
        $this->getEntityManager()->persist($folder);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(Folder $folder): void
    {
        $this->getEntityManager()->remove($folder);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?Folder
    {
        return $this->find($id);
    }

    /** @return Folder[] */
    public function findAll(int $limit, int $offset): array
    {
        return $this->findBy([], ['name' => 'ASC'], $limit, $offset);
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
