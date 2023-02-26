<?php

namespace App\Controller\Front;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateAccountSettingsType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
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

    #[Route('/user/account/settings', name: 'app_user_account_settings')]
    #[Security("is_granted('ROLE_USER')")]
    public function manageAccountSettings(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateAccountSettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            $this->addFlash('success', 'Profile updated!');
            return $this->redirectToRoute('front_app_user_account');
        }

        return $this->renderForm('front/user_account/settings.html.twig', [
            'updateAccountSettingForm' => $form
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

    #[Route('/user/history/{slug}', name: 'app_user_history_details', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
    public function manageProductsHistoryDetails(OrderRepository $orderRepository, Security $security, Order $order): Response
    {
        $user = $security->getUser();
        if ($order->getOwner() !== $user) {
            return $this->redirectToRoute('app_user_history');
        }
        $products = $orderRepository->findAllProductsByOrderId($order->getId());
        return $this->render('front/user_account/history_details.html.twig', [
            'products' => $products,
            'order' => $order,
        ]);
    }
}





