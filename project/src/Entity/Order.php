<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @Table(name="orders")
 */
class Order implements JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    private int $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     * @Groups({"order"})
     */
    private DateTime $expectedTimeDelivery;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"order"})
     */
    private string $deliveryAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"order"})
     */
    private string $billingAddress;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Groups({"order"})
     */
    private int $customerId;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="order", orphanRemoval=true)
     * @MaxDepth(2)
     * @Groups({"order"})
     */
    private Collection $orderItems;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Groups({"order"})
     */
    private string $orderStatus;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_delayed", type="boolean", options={"default": false})
     * @Ignore
     */
    private bool $delayed = false;

    public function __construct() {
        $this->orderItems = new ArrayCollection([]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Order
     */
    public function setId(int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpectedTimeDelivery(): DateTime
    {
        return $this->expectedTimeDelivery;
    }

    /**
     * @param DateTime $expectedTimeDelivery
     * @return Order
     */
    public function setExpectedTimeDelivery(DateTime $expectedTimeDelivery): Order
    {
        $this->expectedTimeDelivery = $expectedTimeDelivery;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryAddress(): string
    {
        return $this->deliveryAddress;
    }

    /**
     * @param string $deliveryAddress
     * @return Order
     */
    public function setDeliveryAddress(string $deliveryAddress): Order
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingAddress(): string
    {
        return $this->billingAddress;
    }

    /**
     * @param string $billingAddress
     * @return Order
     */
    public function setBillingAddress(string $billingAddress): Order
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     * @return Order
     */
    public function setCustomerId(int $customerId): Order
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function addOrderItem(OrderItem $orderItem): Order
    {
        if( is_null($this->orderItems)) {
            $this->orderItems = new ArrayCollection() ;
        }
        $this->orderItems->add($orderItem);
        return $this;
    }

    /**
     * @param OrderItem $orderItem
     * @return $this
     */
    public function removeOrderItem(OrderItem $orderItem): Order
    {
        /**
         * @var $key
         * @var OrderItem $val
         */
        foreach ($this->orderItems as $key => $val) {
            if($orderItem->getId() === $val->getId()) {
                $this->orderItems->remove($key);
                //unset($this->orderItems[$key]);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    /**
     * @param string $orderStatus
     * @return Order
     */
    public function setOrderStatus(string $orderStatus): Order
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDelayed(): bool
    {
        return $this->delayed;
    }

    /**
     * @param bool $delayed
     * @return Order
     */
    public function setDelayed(bool $delayed): Order
    {
        $this->delayed = $delayed;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'id' => $this->getId(),
            'expectedTimeDelivery' => $this->getExpectedTimeDelivery()->format('d.m.Y H:i:s'),
            'deliveryAddress' => $this->getDeliveryAddress(),
            'billingAddress' => $this->getBillingAddress(),
            'customerId' => $this->getCustomerId(),
            'orderStatus' => $this->getOrderStatus(),
            'orderItems' => $this->getOrderItems()
        ];
    }
}
