<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderDelayedRepository;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{
    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     *
     * @Route("/orders/v1/", methods={"POST"})
     */
    public function createOrder(
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        try {
            $entityManager = $doctrine->getManager();
            $params = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $order = new Order();
            $order
                ->setDeliveryAddress($params['deliveryAddress'])
                ->setBillingAddress($params['billingAddress'])
                ->setCustomerId($params['customerId'])
                ->setExpectedTimeDelivery(
                    DateTime::createFromFormat('d.m.Y H:i:s', $params['expectedTimeDelivery']))
                ->setOrderStatus('new')
                ->setDelayed(false)
            ;

            if(!array_key_exists('orderItems', $params) ||
                !is_array($params['orderItems']) || empty($params['orderItems'])) {
                return $this->json(
                    ['status'=> false, 'error' => 'Order items not sent'],
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
            }

            $entityManager->persist($order);
            $entityManager->flush();

            foreach ($params['orderItems'] as $orderItem) {
                $item = new OrderItem();
                $item
                    ->setItemId($orderItem['itemId'])
                    ->setItemQuantity($orderItem['itemQuantity'])
                    ->setOrder($order);

                $entityManager->persist($item);
            }
            $entityManager->flush();
        } catch (Exception $exception) {
            return $this->json(
                ['status'=> false, 'error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }

        return $this->json(
            [
                'status'=> true,
                'error' => '',
                'expectedTimeDelivery' => $order->getExpectedTimeDelivery()->format('d.m.Y H:i:s')
            ],
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param OrderRepository $orderRepository
     * @return Response
     *
     * @Route("/orders/v1/", methods={"PATCH"})
     */
    public function updateOrder(
        Request $request,
        ManagerRegistry $doctrine,
        OrderRepository $orderRepository
    ) : Response
    {
        try {
            $entityManager = $doctrine->getManager();
            $orderId = (int)$request->query->get('orderId');
            $params = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            if(!($orderId > 0) || empty($params) || !array_key_exists('status', $params) ||
                $params['status'] === ''
            ) {
                return $this->json(
                    ['status'=> false, 'error' => 'Bad request'],
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
            }

            $order = $orderRepository->findOneBy(['id' => $orderId]);
            if(!($order instanceof Order)) {
                return $this->json(
                    ['status'=> false, 'error' => 'Order not found'],
                    Response::HTTP_NOT_FOUND,
                    ['content-type' => 'application/json']
                );
            }

            $order->setOrderStatus($params['status']);
            $entityManager->persist($order);
            $entityManager->flush();

        } catch (Exception $exception) {
            return $this->json(
                ['status'=> false, 'error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }


        return $this->json(
            ['status'=> true,'error' => ''],
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     *
     * @Route("/orders/v1/", methods={"GET"})
     */
    public function getOrders(
        Request $request,
        OrderRepository $orderRepository
    ) : Response
    {
        try {

            $orderStatus = $request->query->get('orderStatus');
            $orderId = (int)$request->query->get('orderId');

            $criteria = [];
            if($orderId > 0) {
                $criteria['id'] = $orderId;
            }
            if(!is_null($orderStatus) && $orderStatus !== '') {
                $criteria['orderStatus'] = $orderStatus;
            }

            if(empty($criteria)) {
                return $this->json(
                    ['status'=> false, 'error' => 'Bad request'],
                    Response::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
            }

            $orders = $orderRepository->findBy($criteria);
            //TODO Не выводит внутренности OrderItem
            return $this->json($orders, Response::HTTP_OK, [],
                ['groups' => 'order','content-type' => 'application/json']);

        } catch (Exception $exception) {
            return $this->json(
                ['status'=> false, 'error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }
    }

    /**
     * @param Request $request
     * @param OrderDelayedRepository $orderDelayedRepository
     * @return Response
     *
     * @Route("/orders/v1/delayed", methods={"GET"})
     */
    public function getOrdersDelayed(
        Request $request,
        OrderDelayedRepository $orderDelayedRepository
    ) : Response
    {
        try {
            $from = DateTime::createFromFormat('d.m.Y H:i:s', $request->query->get('from'));
            $to = DateTime::createFromFormat('d.m.Y H:i:s', $request->query->get('to'));
            $orders = $orderDelayedRepository->findByCurrentDate($from, $to);
            return $this->json($orders, Response::HTTP_OK, [],
                ['groups' => ['order-delayed','order'],'content-type' => 'application/json']);
        } catch (Exception $exception) {
            return $this->json(
                ['status'=> false, 'error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }
    }
}
