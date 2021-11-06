<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Vikbert\CloudCronBundle\Entity\CronJob;

/**
 * @method CronJob[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CronJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronJob::class);
    }

    /**
     * @return CronJob[]
     */
    public function findEnabled(): array
    {
        return $this->findBy(['enabled' => true]);
    }
}
