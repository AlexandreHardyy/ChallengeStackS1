<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('front/default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'last_products' => $productRepository->getLastProducts(1),
            'categories' => $categoryRepository->findAll(),
            'five_last_products' => $productRepository->getLastProducts(5),
            'random_products' => $productRepository->getRandomProducts(5),
        ]);
    }
}
