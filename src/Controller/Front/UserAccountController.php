<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Entity\OrderHistory;
use App\Entity\Product;
use App\Repository\OrderHistoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use App\Form\UpdateAccountSettingsType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as SecurityGranted;
use App\Entity\User;
use Vich\UploaderBundle\Handler\DownloadHandler;

class UserAccountController extends AbstractController
{
    #[Route('/user/account', name: 'app_user_account')]
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function manageAccount(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $total = $orderRepository->findBy(['Owner' => $user]);

        return $this->render('front/user_account/index.html.twig', [
            'total' => count($total),
        ]);
    }

    #[Route('/user/account/settings', name: 'app_user_account_settings')]
    #[SecurityGranted("is_granted('ROLE_USER')")]
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
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function manageProductsHistory(OrderRepository $orderRepository, Security $security): Response
    {
        $user = $security->getUser();

        $orders = $orderRepository->findBy(['Owner' => $user]);
        return $this->render('front/user_account/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/user/history/{slug}', name: 'app_user_history_details', methods: ['GET'])]
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function manageProductsHistoryDetails(OrderRepository $orderRepository, Security $security, Order $order): Response
    {
        $user = $security->getUser();
        if ($order->getOwner() !== $user) {
            return $this->redirectToRoute('front_app_user_history');
        }
        $products = $orderRepository->findAllProductsByOrderId($order->getId());
        return $this->render('front/user_account/history_details.html.twig', [
            'products' => $products,
            'order' => $order,
        ]);
    }

    #[Route('/user/get/{slug}/{slugP}', name: 'app_user_history_show', methods: ['GET'])]
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function downloadDocument(Order $order, string $slugP ,DownloadHandler $downloadHandler, ProductRepository $productRepository, OrderHistoryRepository $orderHistoryRepository, Security $security): Response
    {
        $user = $security->getUser();
        $orderHistory = $orderHistoryRepository->findBy(['orders' => $order->getId()], ['timestamp' => 'DESC'], 1, 0);
        if ($order->getOwner() !== $user || $orderHistory[0]->getState()->getId() == 2 || $orderHistory[0]->getState()->getId() == 4) {
            $this->addFlash('error', 'You can\'t download this file! you refund it');
            return $this->redirectToRoute('front_app_user_history');
        }
        $product = $productRepository->findBy(['slug' => $slugP])[0];
        return $downloadHandler->downloadObject($product, 'imageFile', null, $product->getTitle() . '.png');
    }
}





