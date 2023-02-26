<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\OrderRepository;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Vich\UploaderBundle\Handler\DownloadHandler;

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

    #[Route('/user/history/{slug}', name: 'app_user_history_details', methods: ['GET'])]
    #[Security("is_granted('ROLE_USER')")]
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
    #[Security("is_granted('ROLE_USER')")]
    public function downloadDocument(Order $order, string $slugP ,DownloadHandler $downloadHandler, ProductRepository $productRepository, Security $security): Response
    {
        $user = $security->getUser();
        if ($order->getOwner() !== $user) {
            return $this->redirectToRoute('front_app_user_history');
        }
        $product = $productRepository->findBy(['slug' => $slugP])[0];
        return $downloadHandler->downloadObject($product, 'imageFile', null, $product->getTitle() . '.png');
    }
}





