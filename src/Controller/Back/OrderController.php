<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Repository\OrderHistoryRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository, PaginatorInterface $paginator,
    Request $request): Response
    {
        $data = $orderRepository->findBy([], ['id' => 'DESC']);
        $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('back/order/index.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/order/refund/{id}', name: 'app_order_refund')]
    public function refund(Order $order, OrderHistoryRepository $orderHistoryRepository, EntityManagerInterface $em): Response
    {
        $res = $order->getOrderHistories();
        for ($i = 0; $i < count($res); $i++) {
            if ($res[$i]->getState()->getId() === 2) {
                $this->addFlash('error', 'This order is already refunded');
                return $this->redirectToRoute('admin_app_order');
            }
        }
        
        //refund the order 
        $orderHistory = new OrderHistory();
        $status = $em->getRepository(OrderState::class)->find(2); // 2 is the id of the refunded state
        $orderHistory->setOrders($order);
        $orderHistory->setState($status);
        $orderHistory->setTimestamp(new \DateTime());
        $orderHistoryRepository->save($orderHistory,true);

        return $this->redirectToRoute('admin_app_order');

    }

    #[Route('/order/{id}', name: 'app_order_show')]
    public function show(Order $order): Response
    {
        return $this->render('back/order/show.html.twig', [
            'order' => $order
        ]);
    }
}
