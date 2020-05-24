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

    private $response;

    private $infos;

    private $details;



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

            $this->response = self::getAPIId($search);

            return $this->render('admin/getSerie.html.twig', ['series' => $this->response]);
        }

        if (isset($_GET['search_by_id']))
        {
            //on nettoie le input -> static function trim/strip/html
            $id = trim($_GET['search_by_id']);

            $this->infos = self::getInfosWithAPIId($id);
            $this->details = self::getAllDetails($id, sizeof($this->infos->tvSeriesInfo->seasons));

            return $this->render('admin/getSerie.html.twig', ['infos' => $this->infos, 'details' => $this->details]);
        }

        if (isset($_GET['update_bdd']))
        {
            var_dump($this->infos);
            // on utilise les propriétés de $info et $details
            // avec les methodes de Doctrine
            $entityManager = $this->getDoctrine()->getManager();
            $program = new Program();
            $program->setTitle($this->infos->title);
            $program->setCategory();
            $program->setPoster($this->response->results->image);
            $program->setSummary($this->infos->plot);
            $program->setAPIId($this->infos->id);
            $program->setYear($this->infos->releaseDate);
            $program->setAwards($this->infos->awards);
            $program->setNbSeasons(sizeof($this->infos->tvSeriesInfos->seasons));
            $program->setRuntime($this->infos->runtimeMins);

            $entityManager->persist($program);
            $entityManager->flush();
            $entityManager->clear(Program::class);

            // Pour chaque saison
            for ($i=1;$i < sizeof($this->infos->tvSeriesInfos->seasons)+1;$i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setYear($this->details->year);
                $season->setDescription('...');
                $season->setProgram($program);

                $entityManager->persist($season);
                $entityManager->flush();
                $entityManager->clear(Season::class);

                // Pour chaque épisode de la saison
                foreach ($this->details->episodes as $episod) {
                    $episode = new Episode();
                    $episode->setNumber($episod->episodeNumber);
                    $episode->setTitle($episod->title);
                    $episode->setSynopsis($episod->plot);
                    $episode->setPoster($episod->image);
                    $episode->setReleased($episod->released);
                    $episode->setSeason($season);

                    $entityManager->persist($episode);
                    $entityManager->flush();
                }
            }
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