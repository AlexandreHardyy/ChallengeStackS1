<?php

namespace App\Controller\Back;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, 
    PaginatorInterface $paginator,
    Request $request): Response
    {
        $data = $productRepository->findBy([], ['id' => 'DESC']);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('back/product/index.html.twig', [
            'products' =>  $products,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        return $this->render('back/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/ban', name: 'app_product_ban', methods: ['GET'])]
    public function ban(Request $request, Product $product, ProductRepository $productRepository): Response
    {

        if($product->isIsBanned() == true){
            $product->setIsBanned(false);
            $productRepository->save($product, true);
            $this->addFlash('success', 'Product unbanned successfully');
            return $this->redirectToRoute('admin_app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        $product->setIsBanned(true);
        $productRepository->save($product, true);

        $this->addFlash('success', 'Product banned successfully');

        return $this->redirectToRoute('admin_app_product_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/verify', name: 'app_product_verify', methods: ['GET'])]
    public function verify(Request $request, Product $product, ProductRepository $productRepository): Response
    {

        if($product->isIsValid() == true){
            $this->addFlash('success', 'Product already verified');
            return $this->redirectToRoute('admin_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        $product->setIsValid(true);
        $productRepository->save($product, true);

        $this->addFlash('success', 'Product verified successfully');

        return $this->redirectToRoute('admin_app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
