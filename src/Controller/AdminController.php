<?php
namespace App\Controller;

use App\Entity\ApiActor;
use App\Entity\ApiCategory;
use App\Entity\ApiCreator;
use App\Entity\ApiEpisode;
use App\Entity\ApiProgram;
use App\Entity\ApiSeason;
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

        if (isset($_GET['search_by_id']))
        {
            //on nettoie le input -> static function trim/strip/html
            $id = trim($_GET['search_by_id']);

            $infos = self::getInfosWithAPIId($id);
            $details = self::getAllDetails($id, sizeof($infos->tvSeriesInfo->seasons));

            // MaJ BDD API
            $em = $this->getDoctrine()->getManager();

            $program = new ApiProgram();
            $program->setTitle($infos->title);
            $program->setApiId($infos->id);
            $program->setYear(intval($infos->year));
            $program->setPlot($infos->plot);
            $program->setPoster($infos->image);
            $program->setRuntime(intval($infos->runtimeMins));
            $program->setAwards($infos->awards);
            $program->setNbSeasons(sizeof($infos->tvSeriesInfo->seasons));
            $program->setEndYear($infos->tvSeriesInfo->yearEnd);
            $em->persist($program);

            foreach ($infos->actorList as $star) {
                $actor = new ApiActor();
                $actor->setApiId($star->id);
                $actor->setName($star->name);
                $actor->setAsCharacter($star->asCharacter);
                $actor->setImage($star->image);
                $em->persist($actor);
            }

            foreach ($infos->tvSeriesInfo->creatorList as $creater) {
                $creator = new ApiCreator();
                $creator->setApiId($creater->id);
                $creator->setFullName($creater->name);
                $em->persist($creator);
            }

            foreach ($infos->genreList as $genre) {
                $category = new ApiCategory();
                $category->setName($genre->value);
                $em->persist($category);
            }

            for ($i=1;$i<=sizeof($infos->tvSeriesInfo->seasons);$i++) {
                $season = new ApiSeason();
                $season->setNumber($i);
                $season->setYear($details["season_$i"]->year);
                $season->setProgram($program);
                $em->persist($season);

                foreach ($details["season_$i"]->episodes as $episod) {
                    $episode = new ApiEpisode();
                    $episode->setNumber($episod->episodeNumber);
                    $episode->setTitle($episod->title);
                    $episode->setPlot($episod->plot);
                    $episode->setReleased($episod->released);
                    $episode->setImage($episod->image);
                    $episode->setSeason($season);
                    $em->persist($episode);
                }
            }
            $em->flush();
            return $this->render('admin/getSerie.html.twig', ['infos' => $infos, 'details' => $details]);
        }

        if (isset($_GET['update_bdd']))
        {
            // Get Repos API

            // MaJ BDD

            $entityManager = $this->getDoctrine()->getManager();
            $program = new Program();
            $program->setTitle();
            $program->setCategory();
            $program->setPoster();
            $program->setSummary();
            $program->setAPIId();
            $program->setYear();
            $program->setAwards();
            $program->setNbSeasons();
            $program->setRuntime();

            $entityManager->persist($program);
            $entityManager->clear(Program::class);

            // Pour chaque saison
            for ($i=1;$i < sizeof($this->infos->tvSeriesInfos->seasons)+1;$i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setYear();
                $season->setDescription();
                $season->setProgram($program);

                $entityManager->persist($season);
                $entityManager->clear(Season::class);

                // Pour chaque épisode de la saison
                foreach ($this->details->episodes as $episod) {
                    $episode = new Episode();
                    $episode->setNumber();
                    $episode->setTitle();
                    $episode->setSynopsis();
                    $episode->setPoster();
                    $episode->setReleased();
                    $episode->setSeason($season);

                    $entityManager->persist($episode);
                    $entityManager->clear(Episode::class);
                }
            }

            // Clear API BDD




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