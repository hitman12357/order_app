<?php

namespace App\Command;

use App\Entity\Order;
use App\Entity\OrderDelayed;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DelayOrdersCommand extends Command
{
    protected static $defaultName = 'app:delay-orders';
    protected OrderRepository $orderRepository;
    protected ObjectManager $entityManager;

    public function __construct(
        OrderRepository $orderRepository,
        ManagerRegistry $doctrine
    ) {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $doctrine->getManager();
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Move orders to delayed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $currentDate = new DateTime();
            $orders = $this->orderRepository->findDelayedOrders($currentDate);

            /** @var Order $order */
            foreach ($orders as $order) {
                $delayedOrder = new OrderDelayed();
                $delayedOrder
                    ->setOrder($order)
                    ->setCurrentDate($currentDate)
                    ->setExpectedTimeOfDelivery($order->getExpectedTimeDelivery());

                $this->entityManager->persist($delayedOrder);

                $order->setDelayed(true);
                $this->entityManager->persist($order);
            }
            $this->entityManager->flush();
            $output->write("12312312");
            $output->write(var_export($orders, true));

        } catch (Exception $exception) {
            $output->write($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
