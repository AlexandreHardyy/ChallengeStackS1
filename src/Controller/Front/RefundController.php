<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Repository\OrderHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RefundController extends AbstractController
{
    #[Route('/refund/{id}', name: 'app_refund')]
    public function index(Order $order, EntityManagerInterface $em, OrderHistoryRepository $orderHistoryRepository): Response
    {
        $res = $order->getOrderHistories();
        for ($i = 0; $i < count($res); $i++) {
            if ($res[$i]->getState()->getId() === 3) {
                return $this->redirectToRoute('front_app_user_history');
            }
        }
        $orderHistory = new OrderHistory();
        $status = $em->getRepository(OrderState::class)->find(3);
        $orderHistory->setOrders($order);
        $orderHistory->setState($status);
        $orderHistory->setTimestamp(new \DateTime());
        $orderHistoryRepository->save($orderHistory,true);

        return $this->redirectToRoute('front_app_user_history');
    }
}
