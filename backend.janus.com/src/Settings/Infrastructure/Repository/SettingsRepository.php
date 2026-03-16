<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Repository;

use App\Settings\Domain\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SettingsRepository extends ServiceEntityRepository
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
