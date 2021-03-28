<?php

declare(strict_types=1);

namespace App\Actions;

use App\Repository\CategoryRepository;
use App\Responders\ViewResponder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class Category
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/trick/category/{slug}", name="trick_category")
     * @param $slug
     * @param ViewResponder $responder
     * @return Response
     */
    public function __invoke($slug, ViewResponder $responder): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw new NotFoundHttpException("This trick's category does not exist");
        }

        return $responder('category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }
}
