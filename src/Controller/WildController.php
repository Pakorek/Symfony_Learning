<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/wild", name="wild_")
 *
 * Class WildController
 * @package App\Controller
 */
class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table');
        }


        return $this->render('wild/index.html.twig', ['programs' => $programs]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>?null}", name="show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug
        ]);
    }

    /**
     * @Route("/category/{categoryName<^[a-zA-Z]+$>?null}", name="show_category")
     *
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName):Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find programs');
        }

        // En partant du principe que toutes les catégories sont au format Capitalize (BDD)
        // Et pour pallier l'éventuel erreur type 'science_fiction' au lieu de 'science-fiction'
        $categoryName = preg_replace(
            '/_/',
            '-', ucwords(trim(strip_tags($categoryName)), "_")
        );

        // On récupère l'Objet $category
        // On pourra ainsi récupérer le category_id correspondant au categoryName
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category->getId()], ['id' => 'DESC'], 3);

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
        ]);
    }
}