<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    /**
     * @param CategoryRepository $categoryRepo
     * @return Response
     */
    public function index(CategoryRepository $categoryRepo)
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepo->findAll(),
        ]);
    }
}
