<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Repository;

use App\Settings\Domain\Entity\Settings;
use App\Settings\Domain\Repository\SettingsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Settings>
 */
final class SettingsRepository extends ServiceEntityRepository implements SettingsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    public function getOrCreate(): Settings
    {
        $settings = $this->findOneBy([]);

        if ($settings === null) {
            $settings = new Settings();
            $this->getEntityManager()->persist($settings);
            $this->getEntityManager()->flush();
        }

        return $settings;
    }

    public function save(Settings $settings): void
    {
        $this->getEntityManager()->persist($settings);
        $this->getEntityManager()->flush();
    }
}
