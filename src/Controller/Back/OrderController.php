<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Repository\OrderHistoryRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('back/order/index.html.twig', [
            'orders' => $orderRepository->findAll()
        ]);
    }

    #[Route('/order/refund', name: 'app_order_refund')]
    public function refund(OrderHistoryRepository $orderHistoryRepository): Response
    {
        /*$orderHistory = new OrderHistory();
        $status = $em->getRepository(OrderState::class)->find(2);
        $orderHistory->setOrders($order);
        $orderHistory->setState($status);
        $orderHistory->setTimestamp(new \DateTime());
        $orderHistoryRepository->save($orderHistory,true);*/

        return $this->render('back/order/refund.html.twig', [
            'orders' =>  $orderHistoryRepository->findBy(['state' => 3])
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_show')]
    public function show(Order $order): Response
    {
        return $this->render('back/order/show.html.twig', [
            'order' => $order
        ]);
    }
}
