<?php

declare(strict_types = 1);

namespace Vikbert\CloudCronBundle\Repository;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Vikbert\CloudCronBundle\Entity\CronJob;
use Vikbert\CloudCronBundle\Entity\CronReport;

final class CronReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CronReport::class);
    }

    public function countByJobAndDueTime(CronJob $cronJob, DateTimeImmutable $dueTime): int
    {
        return $this->count(
            [
                'dueTime' => $dueTime,
                'jobId' => $cronJob->getId(),
            ]
        );
    }

    /**
     * @throws ORMException
     */
    public function save(CronReport $cronReport): void
    {
        $em = $this->getEntityManager();

        $em->persist($cronReport);
        $em->flush();
    }
}
