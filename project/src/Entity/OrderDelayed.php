<?php

namespace App\Entity;

use App\Repository\OrderDelayedRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OrderDelayedRepository::class)
 * @Table(name="orders_delayed")
 */
class OrderDelayed
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"order-delayed"})
     */
    private int $id;

    /**
     * @var Order
     *
     * @ORM\OneToOne(targetEntity=Order::class, cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"order-delayed"})
     */
    private Order $order;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", name="curr_date")
     * @Groups({"order-delayed"})
     */
    private DateTime $currentDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Groups({"order-delayed"})
     */
    private DateTime $expectedTimeOfDelivery;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return OrderDelayed
     */
    public function setId(int $id): OrderDelayed
    {
        $this->id = $id;
        return $this;
    }



    /**
     * @return DateTime
     */
    public function getCurrentDate(): DateTime
    {
        return $this->currentDate;
    }

    /**
     * @param DateTime $currentDate
     * @return OrderDelayed
     */
    public function setCurrentDate(DateTime $currentDate): OrderDelayed
    {
        $this->currentDate = $currentDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpectedTimeOfDelivery(): DateTime
    {
        return $this->expectedTimeOfDelivery;
    }

    /**
     * @param DateTime $expectedTimeOfDelivery
     * @return OrderDelayed
     */
    public function setExpectedTimeOfDelivery(DateTime $expectedTimeOfDelivery): OrderDelayed
    {
        $this->expectedTimeOfDelivery = $expectedTimeOfDelivery;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return OrderDelayed
     */
    public function setOrder(Order $order): OrderDelayed
    {
        $this->order = $order;
        return $this;
    }
}
