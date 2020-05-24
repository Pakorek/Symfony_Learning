<?php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/admin", name="admin_")
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    const KEY = 'k_Gyn239Fh';

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index():Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/getSerie", name="getSerie")
     *
     * @return Response
     */
    public function getSerie():Response
    {
        if (isset($_GET['search_id']))
        {
            //on nettoie le input -> static function trim/strip/html
            $search = trim($_GET['search_id']);

            $response = self::getAPIId($search);

            return $this->render('admin/getSerie.html.twig', ['series' => $response]);
        }

        $infos = $details = null;
        if (isset($_GET['search_by_id']))
        {
            //on nettoie le input -> static function trim/strip/html
            $id = trim($_GET['search_by_id']);

            $infos = self::getInfosWithAPIId($id);
            $details = self::getAllDetails($id, sizeof($infos->tvSeriesInfo->seasons));

            return $this->render('admin/getSerie.html.twig', ['infos' => $infos, 'details' => $details]);
        }

        if (isset($_GET['update_bdd']))
        {
            // on utilise les propriétés de $info et $details
            // avec les methodes de Doctrine
            $entityManager = $this->getDoctrine()->getManager();

            $program = new Program();
            $program->setTitle($infos->id);
            $program->setCategory();
            $program->setPoster();
            $program->setSummary();
            $program->setAPIId();

            $entityManager->persist($program);
            $entityManager->flush();
            $entityManager->clear(Program::class);

            // loop
            $season = new Season();
            $season->setNumber();
            $season->setYear();
            $season->setDescription();
            $season->setProgram();

            $entityManager->persist($season);

            // loop in loop
            $episode = new Episode();
            $episode->setNumber();
            $episode->setTitle();
            $episode->setSynopsis();
            $episode->setSeason();

            $entityManager->persist($episode);



            return $this->render('admin/getSerie.html.twig');

        }

        return $this->render('admin/getSerie.html.twig');
    }

    /**
     * get API id from IMDB API
     * and pick up the official title format
     *
     * @param string $search
     * @return mixed
     */
    public static function getAPIId(string $search)
    {
        // appliquer une fonction à $search pour les cas avec plusieurs mots
        // ex: Breaking Bad         (un truc du genre replace(' ','%20',$search)

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://imdb-api.com/en/API/SearchSeries/". self::KEY . "/$search",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    /**
     * get details from one program with API_id
     *
     * @param string $id
     * @return mixed
     */
    public static function getInfosWithAPIId(string $id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://imdb-api.com/en/API/Title/". self::KEY ."/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    /**
     * get details from each season
     *
     * @param string $id
     * @param int $seasons
     * @return array
     */
    public static function getAllDetails(string $id, int $seasons):array
    {
        $details = [];
        $curl = curl_init();

        for ($i=1;$i<$seasons+1;$i++) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://imdb-api.com/en/API/SeasonEpisodes/". self::KEY ."/$id/$i",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));

            $response = curl_exec($curl);

            $details["season_$i"] = json_decode($response);
        }

        curl_close($curl);

        return $details;
    }
}