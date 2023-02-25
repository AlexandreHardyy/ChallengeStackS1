<?php

namespace App\Controller\Front;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use App\Form\ProductType;
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
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        if ($request->query->get('search')) {
            $search = $request->query->get('search');
            $result = $productRepository->getProductsWithSearch($search);
        } else {
            $result = $productRepository->findAll();
        }

        return $this->render('front/product/index.html.twig', [
            'products' => $result,
            'last_products' => $productRepository->getLastProducts(1),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function new(Request $request,ManagerRegistry $doctrine, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $entityManager = $doctrine->getManager();
        // $category = new Category();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData()->getCategories();
            foreach ($form_data as $datum){
                $product->addCategory($datum);
            }
            $product->setCreator($this->getUser());
            $product->setIsActive(false);
            $product->setIsBanned(false);
            $product->setIsValid(false);
            $entityManager->persist($product);
            $entityManager->flush();
            //$productRepository->save($product, true);
            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('front/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('EDIT_PRODCUT', $product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form_data = $form->getData()->getCategories();
            foreach ($form_data as $datum){
                $product->addCategory($datum);
            }
            // Loop categories
            $productRepository->save($product, true);
            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[Security("is_granted('ROLE_SELLER')")]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
