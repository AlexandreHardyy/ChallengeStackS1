<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator,
    Request $request): Response
    {
        $data = $categoryRepository->findBy([], ['id' => 'DESC']);
        $category = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('back/category/index.html.twig', [
            'categories' => $category,
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($categoryRepository->findBy(['name' => $category->getName()])){
                $this->addFlash('error', 'Cette catégorie existe déjà');
                return $this->redirectToRoute('admin_app_category_index', [], Response::HTTP_SEE_OTHER);
            }
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('admin_app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            
            if($categoryRepository->findBy(['name' => $category->getName()])){
                $this->addFlash('error', 'Cette catégorie existe déjà');
                return $this->redirectToRoute('admin_app_category_index', [], Response::HTTP_SEE_OTHER);
            }
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('admin_app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('admin_app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
