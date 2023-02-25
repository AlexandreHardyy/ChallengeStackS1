<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\OrderRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class UserAccountController extends AbstractController
{
    #[Route('/user/account', name: 'app_user_account')]
    #[Security("is_granted('ROLE_USER')")]
    public function manageAccount(): Response
    {
        return $this->render('front/user_account/index.html.twig', [
            'controller_name' => 'UserAccountController',
        ]);
    }

    #[Route('/user/history', name: 'app_user_history', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function manageProductsHistory(OrderRepository $orderRepository, Security $security): Response
    {
        $user = $security->getUser();

        $orders = $orderRepository->findBy(['Owner' => $user]);
        return $this->render('front/user_account/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/user/history/{id}', name: 'app_user_history_details', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function manageProductsHistoryDetails(OrderRepository $orderRepository, Security $security, int $id): Response
    {
        $user = $security->getUser();
        $order = $orderRepository->find($id);
        if ($order->getOwner() !== $user) {
            return $this->redirectToRoute('app_user_history');
        }
        $products = $orderRepository->findAllProductsByOrderId($id);
        return $this->render('front/user_account/history_details.html.twig', [
            'products' => $products,
            'order' => $order,
        ]);
    }
}





