<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
class OrderItem implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"order", "order_item"})
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order", "order_item"})
     */
    private int $itemId;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"order", "order_item"})
     */
    private int $itemQuantity;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity=Order::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Order $order;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return OrderItem
     */
    public function setId(int $id): OrderItem
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     * @return OrderItem
     */
    public function setItemId(int $itemId): OrderItem
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemQuantity(): int
    {
        return $this->itemQuantity;
    }

    /**
     * @param int $itemQuantity
     * @return OrderItem
     */
    public function setItemQuantity(int $itemQuantity): OrderItem
    {
        $this->itemQuantity = $itemQuantity;
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
     * @return OrderItem
     */
    public function setOrder(Order $order): OrderItem
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'id' => $this->getId(),
            'itemId' => $this->getItemId(),
            'itemQuantity' => $this->getItemQuantity()
        ];
    }
}
