<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
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
        // Et pour pallier l'éventuelle erreur type 'science_fiction' au lieu de 'science-fiction'
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

    /**
     * @Route("/program/{programName<^[a-zA-Z0-9-, &]+$>?null}", name="show_program")
     *
     * @param string $programName
     * @return Response
     */
    public function showByProgram(string $programName):Response
    {
        if (!$programName) {
            throw $this
                ->createNotFoundException('No program has been sent');
        }

        $programName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($programName)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => $programName]);

        $seasons = $program->getSeasons()->getValues();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/season/{id<^[0-9]+$>?null}", name="show_season")
     *
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id):Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No season\'s id has been sent');
        }

        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['id' => $id]);

        $program = $season->getProgram()->getTitle();
        $episodes = $season->getEpisodes()->getValues();

        return $this->render('wild/season.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes
        ]);
    }

    /**
     * @Route("/episode/{episode}", name="show_episode")
     *
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {
        if (!$episode) {
            throw $this
                ->createNotFoundException('No episode\'s id has been sent');
        }

        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program
        ]);
    }
}
