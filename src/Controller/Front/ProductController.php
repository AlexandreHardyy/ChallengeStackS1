<?php

namespace App\Controller\Front;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductTypeUpdate;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository,  Request $request): Response
    {
        $category = null;
        if ($request->query->get('search') ||  $request->query->get('category')) {
            $search = $request->query->get('search');
            $category_id = $request->query->get('category');

            if($search) $result = $productRepository->getProductsWithSearch($search);
            else {
                $category = $categoryRepository->find($category_id)->getName();
                $result = $productRepository->getProductsWithCategory($category_id);
            }
        } else {
            $result = $productRepository->getAllProductsValid();
        }

        return $this->render('front/product/index.html.twig', [
            'products' => $result,
            'category' => $category,
            'last_products' => $productRepository->getLastProducts(1),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreator($this->getUser());
            $product->setIsActive(false);
            $product->setIsBanned(false);
            $product->setIsValid(false);
            $productRepository->save($product, true);

            $this->addFlash('success', 'Votre produit a bien été ajouté, il sera validé par un administrateur dans les plus brefs délais.');
            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        if ($product->isIsBanned() || !$product->isIsActive() || !$product->isIsValid()) {
            $this->addFlash('error', 'Ce produit n\'existe pas.');
            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('front/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('EDIT_PRODCUT', $product);

        $form = $this->createForm(ProductTypeUpdate::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            $this->addFlash('success', 'Votre produit a bien été modifié.');
            return $this->redirectToRoute('front_app_seller', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_product_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
           $product->setIsBanned(true);
              $productRepository->save($product, true);
        }

        $this->addFlash('success', 'Votre produit a bien été supprimé.');
        return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
