<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Repository;

use App\Files\Domain\Entity\File;
use App\Files\Domain\Repository\FileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
final class FileRepository extends ServiceEntityRepository implements FileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function save(File $file, bool $flush = true): void
    {
        $this->getEntityManager()->persist($file);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(File $file): void
    {
        $this->getEntityManager()->remove($file);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?File
    {
        return $this->find($id);
    }

    /** @return File[] */
    public function findPaginated(int $limit, int $offset, ?string $folderId = null): array
    {
        $criteria = $folderId !== null ? ['folder' => $folderId] : [];
        return $this->findBy($criteria, ['createdAt' => 'DESC'], $limit, $offset);
    }

    public function countAll(?string $folderId = null): int
    {
        $criteria = $folderId !== null ? ['folder' => $folderId] : [];
        return $this->count($criteria);
    }
}
