<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(OrderRepository $oderRepo, ProductRepository $productRepo): Response
    {
        //get the total number of orders from the last 30 days
        $orders = $oderRepo->findByLast30Days();
        $totalOrders = count($orders);
        //get the total number of products
        $products = $productRepo->findByLast30Days();
        $totalProducts = count($products);

        $this->addFlash('success', 'Welcome to the back office');
        return $this->render('back/default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts
        ]);
    }

}
