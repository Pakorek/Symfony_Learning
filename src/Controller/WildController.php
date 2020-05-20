<?php
namespace App\Controller;

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
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig', ['website' => 'Wild Series']);
    }

    /**
     * @Route("/show/{slug<[a-z0-9-]*>}", name="show")
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug = null): Response
    {
        if ($slug) {
            $slug = ucwords(implode(" ", explode("-", $slug)));
        }
        return $this->render('wild/show.html.twig', ['slug' => $slug]);
    }
}