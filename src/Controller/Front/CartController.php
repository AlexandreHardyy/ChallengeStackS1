<?php

namespace App\Controller\Front;

use App\Entity\Cart;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as SecurityGranted;


use App\Entity\Order;
use App\Entity\OrderDetails;

#[Route('/cart')]
class CartController extends AbstractController
{
    /**
     * @throws ApiErrorException
     */
    #[Route('/create/order', name: 'app_cart_create_order', methods: ['POST'])]
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function createOrder(EntityManagerInterface $em, ProductRepository $productRepository,  MailerInterface $mailer): Response
    {
        $cart = $this->getUser()->getCart();
        $total = 0;


        $orderHistory = new OrderHistory();
        $status = $em->getRepository(OrderState::class)->find(1);

        $stripeService = new StripeService();
        $resource = $productRepository->stripePayment($_POST);

        if($resource !== null){
            $totalPrice = array_reduce($cart->getProducts()->toArray(), function($sum, $product) {
                return $sum + $product->getPrice();
            }, 0.0);
            $productTitles = implode(', ', array_map(function($product) {
                return $product->getTitle();
            }, $cart->getProducts()->toArray()));
            $order = new Order();
            $order->setOwner($this->getUser());

            $order->setTotalPaid($total);
            $order->setTotalPaid($totalPrice + ($totalPrice * 0.2));
            $order->setBrandStripe($resource['stripeBrand']);
            $order->setLast4Stripe($resource['stripeLast4']);
            $order->setIdChargeStripe($resource['stripeId']);
            $order->setStripeToken($resource['stripeToken']);
            $order->setStatusStripe($resource['stripeStatus']);
            $order->setReference();
            $order->setCreatedAt(new \DateTime());
            $order->setUpdatedAt(new \DateTime());

           
            // Loop through products in cart and add them to order
            $receipt_details = array(); 
            foreach ($cart->getProducts() as $product) {
                $orderDetail = new OrderDetails();
                $orderDetail->setOrderId($order);
                $orderDetail->setDescription($productTitles);
                $orderDetail->setProductId($product);
                $orderDetail->setPrice($product->getPrice());
                $total += $product->getPrice();
                $em->persist($orderDetail);
                
                $receipt_details[] = array(
                    'title' => $product->getTitle(),
                    'amount' => $product->getPrice(),
                );

                // Remove product from cart
                $cart->removeProduct($product);
                $em->persist($cart);
            }

    
            //send email
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@vgcreator.fr', 'PixelFusion'))
                ->to($this->getUser()->getEmail())
                ->subject('Confirmation de votre commande')
                ->htmlTemplate('front/order/order_receipt.html.twig')
                ->context([
                    'order' => $order,
                    'receipt_details' => $receipt_details,
                ])
            ;

            $mailer->send($email);


            $orderHistory->setOrders($order);
            $orderHistory->setState($status);
            $orderHistory->setTimestamp(new \DateTime());

            $em->persist($order);
            $em->persist($orderHistory);
            $em->flush();

            $this->addFlash('success', 'Votre commande a bien été prise en compte');
            return $this->redirect('/orders');
        } else{
            return $this->redirect('/cart', 500);
        }
    }


    /**
     * @throws ApiErrorException
     */
    #[Route('/', name: 'app_cart_show', methods: ['GET'])]
    #[SecurityGranted("is_granted('ROLE_USER')")]
    public function show(ProductRepository $productRepository): Response
    {
        $cart = $this->getUser()->getCart();
        $cart_products = $cart->getProducts()->toArray();
        return $this->render('front/cart/show.html.twig', [
            'cart' => $cart,
            'intentSecret'=> count($cart_products) > 0?
                $productRepository->getIntentSecret($cart_products):
                null
        ]);
    }

    #[Route('/new/{id}', name: 'app_cart_new', methods: ['GET', 'POST'])]
    #[SecurityGranted("is_granted('ROLE_USER')")]
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
    #[SecurityGranted("is_granted('ROLE_USER')")]
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
