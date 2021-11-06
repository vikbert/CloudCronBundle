<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Repository;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Vikbert\CloudCronBundle\Entity\CronJob;
use Vikbert\CloudCronBundle\Entity\CronReport;

final class CronReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronReport::class);
    }

    public function foundJobForDueTime(CronJob $cronJob, DateTimeImmutable $dueTime): bool
    {
        $counter = $this->count(
            [
                'dueTime' => $dueTime,
                'jobId' => $cronJob->getId(),
            ]
        );

        return $counter > 0;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(CronReport $cronReport): void
    {
        $em = $this->getEntityManager();

        $em->persist($cronReport);
        $em->flush();
    }
}
