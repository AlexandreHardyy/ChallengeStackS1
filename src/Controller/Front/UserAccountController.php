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

        // Check if the user has orders in the database
        $orders = $orderRepository->findProductsByUser($user);

        return $this->render('front/user_account/history.html.twig', [
            'orders' => $orders,
        ]);
    }
}





