<?php

namespace App\Repository;

use App\Entity\OrderDelayed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class OrderDelayedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDelayed::class);
    }

    public function findByOrderIdOrderStatus(DateTime $from, DateTime $to)
    {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->where ($qb->expr()->gt('o.currentDate',':dateFrom'))
            ->andWhere ($qb->expr()->lt('o.currentDate',':dateTo'))
            ->setParameter('dateFrom', $from)
            ->setParameter('dateTo', $to)
            ->getQuery();

        return $qb->getQuery()->getResult();
    }
}
