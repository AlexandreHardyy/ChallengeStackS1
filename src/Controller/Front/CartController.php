<?php

namespace App\Controller\Front;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{

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
        return $this->redirectToRoute('front_app_cart_show', ['id' => $cart->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart): Response
    {
        return $this->render('front/cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getUser()->getCart();
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $cart->removeProduct($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('front_app_cart_show', ['id' => $cart->getId()], Response::HTTP_SEE_OTHER);
    }
}
