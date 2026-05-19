<?php

declare(strict_types=1);

namespace App\Files\Infrastructure\Repository;

use App\Files\Domain\Entity\File;
use App\Files\Domain\Entity\Folder;
use App\Files\Domain\Repository\FileRepositoryInterface;
use App\Files\Infrastructure\Persistence\Doctrine\Entity\FileEntity;
use App\Files\Infrastructure\Persistence\Doctrine\Mapper\FileMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FileEntity>
 */
final class FileRepository extends ServiceEntityRepository implements FileRepositoryInterface
{
    public function __construct(
        ManagerRegistry          $registry,
        private readonly FileMapper $mapper,
    ) {
        parent::__construct($registry, FileEntity::class);
    }

    public function save(File $domain, bool $flush = true): void
    {
        $entity = $this->getEntityManager()->find(FileEntity::class, $domain->getId());

        if ($entity === null) {
            $entity = $this->mapper->toPersistence($domain);
            $this->getEntityManager()->persist($entity);
        } else {
            $entity->setFilenameDownload($domain->getFilenameDownload())
                   ->setTitle($domain->getTitle())
                   ->setUploadedBy($domain->getUploadedBy())
                   ->setUpdatedAt($domain->getUpdatedAt());
        }

        $folder = $domain->getFolderId() !== null
            ? $this->getEntityManager()->find(Folder::class, $domain->getFolderId())
            : null;
        $entity->setFolder($folder);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete(File $domain): void
    {
        $entity = $this->getEntityManager()->find(FileEntity::class, $domain->getId());
        if ($entity !== null) {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }
    }

    public function findById(string $id): ?File
    {
        $entity = $this->getEntityManager()->find(FileEntity::class, $id);
        return $entity !== null ? $this->mapper->toDomain($entity) : null;
    }

    /** @return File[] */
    public function findPaginated(int $limit, int $offset, ?string $folderId = null): array
    {
        $qb = $this->createQueryBuilder('f')
                   ->orderBy('f.createdAt', 'DESC')
                   ->setMaxResults($limit)
                   ->setFirstResult($offset);

        if ($folderId !== null) {
            $qb->andWhere('IDENTITY(f.folder) = :folderId')
               ->setParameter('folderId', $folderId);
        }

        $entities = $qb->getQuery()->getResult();
        return array_map(fn(FileEntity $e) => $this->mapper->toDomain($e), $entities);
    }

    public function countAll(?string $folderId = null): int
    {
        $qb = $this->createQueryBuilder('f')
                   ->select('COUNT(f.id)');

        if ($folderId !== null) {
            $qb->andWhere('IDENTITY(f.folder) = :folderId')
               ->setParameter('folderId', $folderId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
