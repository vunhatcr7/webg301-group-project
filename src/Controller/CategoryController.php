<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route(name: 'app_category_index', methods: ['GET'])]
public function index(Request $request, CategoryRepository $categoryRepository): Response
{
    $keyword = $request->query->get('keyword');

    $categories = $categoryRepository->findByName($keyword);

    return $this->render('category/index.html.twig', [
        'categories' => $categories,
        'keyword' => $keyword,
    ]);
}

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Tính toán giá trị position dựa trên số lượng hiện tại trong bảng
            $maxPosition = $categoryRepository->createQueryBuilder('c')
                ->select('MAX(c.Position)')
                ->getQuery()
                ->getSingleScalarResult();

            $category->setPosition($maxPosition + 1);

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully.');
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The category has been updated successfully.'
            );
            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Category $category, 
        EntityManagerInterface $entityManager, 
        CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'The category has been deleted successfully.'
            );
            $categories = $categoryRepository->createQueryBuilder('c')
                ->orderBy('c.Position', 'ASC')
                ->getQuery()
                ->getResult();
            $position = 1;
            foreach ($categories as $remainingCategory) {
                $remainingCategory->setPosition($position);
                $position++;
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
