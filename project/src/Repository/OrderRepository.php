<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findDelayedOrders(DateTime $currentDate) {
        $qb = $this->createQueryBuilder('o');
        $qb
            ->where('o.delayed = :delayed')
            ->andWhere($qb->expr()->lt('o.expectedTimeDelivery',':date'))
            ->setParameter('delayed', false)
            ->setParameter('date', $currentDate)
        ;

        return $qb->getQuery()->getResult();
    }
}
