<?php

namespace Kishron\ReleaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/rally")
 */
class ApiRallyController extends Controller
{
    /**
     * @Route("/releases", name="ApiRallyReleases")
     * @Method({"GET"})
     */
    public function activeReleasesAction()
    {
        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'jleon@alertlogic.com', '2hL}Vo}UgyZ5');
        $json = $rally->execute(
                'release',
                '((Project.ObjectUUID = a7ee09d8-d5a6-48d0-bf1b-7a0b4ff71b94) and (State = Planning))',
                'ReleaseDate DESC',
                'Name,ReleaseDate,ObjectUUID'
            );
        $rally->close();
        //ob_start('ob_gzhandler');
        return new JsonResponse(json_decode($json));
    }
    
    /**
     * @Route("/storiesByRelease", name="ApiRallyReleases")
     * @Method({"GET"})
     */
    public function storiesByReleaseAction(Request $request)
    {
        $nameRelease = $request->query->get('nameRelease');
        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'jleon@alertlogic.com', '2hL}Vo}UgyZ5');
        $jsonDefects = $rally->execute(
                'defect',
                '(Release.Name = "' . $nameRelease . '")',
                'FormattedID ASC',
                'ObjectUUID,FormattedID'
            );
        $jsonStories = $rally->execute(
                'hierarchicalrequirement',
                '(Release.Name = "' . $nameRelease . '")',
                'FormattedID ASC',
                'ObjectUUID,FormattedID'
            );
        $arrayDefects = json_decode($jsonDefects, true);
        $arrayStories = json_decode($jsonStories, true);
        
        $rally->close();
        $json = '{"Defects": ' 
                . json_encode($arrayDefects['QueryResult']['Results']) 
                . ', "Stories": ' 
                . json_encode($arrayStories['QueryResult']['Results']) 
                . '}';
        return new JsonResponse(json_decode($json));
    }
}
