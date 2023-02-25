<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('front/user_account/history.html.twig', [
            'orders' => $orders,
        ]);
    }
}
