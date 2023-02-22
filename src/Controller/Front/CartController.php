<?php

namespace App\Controller\Front;

use App\Entity\Cart;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Entity\Product;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;
use App\Entity\OrderDetails;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/fake', name: 'app_cart_fake', methods: ['GET'])]
    public function fake(EntityManagerInterface $em): Response
    {
        $cart = $this->getUser()->getCart();
        $total = 0;

        $order = new Order();
        $orderHistory = new OrderHistory();
        $status = $em->getRepository(OrderState::class)->find(1);

        // Loop through products in cart and add them to order
        foreach ($cart->getProducts() as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->setOrderId($order);
            $orderDetail->setProductId($product);
            $orderDetail->setPrice($product->getPrice());
            $total += $product->getPrice();
            $em->persist($orderDetail);

            // Remove product from cart
            $cart->removeProduct($product);
            $em->persist($cart);
        }

        $order->setOwner($this->getUser());
        $order->setTotalPaid($total);
        $order->setCreatedAt(new \DateTime());
        $order->setUpdatedAt(new \DateTime());
        $orderHistory->setOrders($order);
        $orderHistory->setState($status);
        $orderHistory->setTimestamp(new \DateTime());

        $em->persist($order);
        $em->persist($orderHistory);
        $em->flush();

        return $this->render('front/user_account/history.html.twig', [
            'orders' => $order->getOrderDetails(),
        ]);
    }

    #[Route('/', name: 'app_cart_show', methods: ['GET'])]
    public function show(): Response
    {
        $cart = $this->getUser()->getCart();
        return $this->render('front/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/new/{id}', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function new(Product $product, CartRepository $cartRepository): Response
    {
        $user = $this->getUser();
        if ($user->getCart()) {
            $cart = $user->getCart();
        } else {
            $cart = new Cart();
            $cart->setOwner($user);
        }
        $cart->addProduct($product);
        $cartRepository->save($cart, true);
        return $this->redirectToRoute('front_app_cart_show', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getUser()->getCart();
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $cart->removeProduct($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_app_cart_show', [],Response::HTTP_SEE_OTHER);
    }
}
