<?php

namespace Kishron\ReleaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Kishron\ReleaseBundle\Entity\ReleaseObj;
use Kishron\ReleaseBundle\Entity\Story;

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
                '((Project.ObjectUUID = a7ee09d8-d5a6-48d0-bf1b-7a0b4ff71b94) and (State = Active))',
                'ReleaseDate DESC',
                'Name,ReleaseDate,ObjectUUID'
            );
        $rally->close();
        //ob_start('ob_gzhandler');
        return new JsonResponse(json_decode($json));
    }
    
    /**
     * @Route("/storiesByRelease", name="ApiRallyStoriesByRelease")
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
    
    /**
    * @Route("/artifactsByRelease", name="ApiRallyArtifactsByRelease")
    * @Method({"GET"})
    */
    public function artifactsByReleaseAction(Request $request) {
        $releaseId = $request->query->get('releaseId');
        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'jleon@alertlogic.com', '2hL}Vo}UgyZ5');
        $jsonDefects = $rally->execute(
                'defect', 
                '(Release.ObjectUUID = "' . $releaseId . '")', 
                'FormattedID ASC', 
                'ObjectUUID,FormattedID,Owner'
        );
        $jsonStories = $rally->execute(
                'hierarchicalrequirement', 
                '(Release.ObjectUUID = "' . $releaseId . '")', 
                'FormattedID ASC', 
                'ObjectUUID,FormattedID,Owner'
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
    
    /**
    * @Route("/createArtifactsAndRelease", name="ApiRallyCreateArtifactsAndRelease")
    * @Method({"POST"})
    */
    public function createArtifactsAndReleaseAction(Request $request) {
        $releaseId = $request->request->get('releaseId');
        $artifacts = $request->request->get('artifacts');
        $em = $this->getDoctrine()->getManager();
        $releaseRepo = $em->getRepository('ReleaseBundle:ReleaseObj');
        $storyRepo = $em->getRepository('ReleaseBundle:Story');
        
        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'jleon@alertlogic.com', '2hL}Vo}UgyZ5');
        
        $jsonRelease = $rally->execute(
                'release',
                '(ObjectUUID = "' . $releaseId . '")',
                '',
                'Name,ReleaseDate,ObjectUUID,Project'
            );
        
        $jsonDefects = $rally->execute(
                'defect', 
                '(Release.ObjectUUID = "' . $releaseId . '")', 
                'FormattedID ASC', 
                'ObjectID,ObjectUUID,FormattedID,TestCases,Owner'
        );
        $jsonStories = $rally->execute(
                'hierarchicalrequirement', 
                '(Release.ObjectUUID = "' . $releaseId . '")', 
                'FormattedID ASC', 
                'ObjectID,ObjectUUID,FormattedID,TestCases,Owner'
        );
        
        $arrayDefects = json_decode($jsonDefects, true);
        $arrayStories = json_decode($jsonStories, true);
        $arrayRelease = json_decode($jsonRelease, true);
        
        $projectID =substr($arrayRelease['QueryResult']['Results'][0]['Project']['_ref'], 
            strrpos($arrayRelease['QueryResult']['Results'][0]['Project']['_ref'], "/") + 1);

        // create the release if don't exist
        $release = $releaseRepo->findOneBy(array(
            'objectUUID' => $releaseId
        ));
        if (!$release) {
            $release = new ReleaseObj();
            $release->setActive(1);
            $release->setSuccess(0);
            $release->setCode($arrayRelease['QueryResult']['Results'][0]['Name']);
            $release->setDate(new \DateTime($arrayRelease['QueryResult']['Results'][0]['ReleaseDate']));
            $release->setObjectUUID($arrayRelease['QueryResult']['Results'][0]['ObjectUUID']);
            $em->persist($release);
        }
        
        foreach ($arrayDefects['QueryResult']['Results'] as $result) {
            if (in_array($result['FormattedID'], $artifacts) ) {
                $story = $storyRepo->findOneBy(array(
                    'objectUUID' => $result['ObjectUUID']
                ));
                if (!$story) {
                    $story = new Story();
                    $story->setCode($result['FormattedID']);
                    $story->setName($result['_refObjectName']);
                    $story->setObjectUUID($result['ObjectUUID']);
                    $story->setOwner($result['Owner']['_refObjectName']);
                    $testRun = '';
                    if ( (int) $result['TestCases']['Count']>0) {
                        $testRun = '/testrun';
                    }
                    $url = "https://rally1.rallydev.com/#/" . $projectID . "d/detail/defect/"
                        .$result['ObjectID'] . $testRun;
                    $story->setRallyUrl($url);
                    $story->setRelease($release);
                    $em->persist($story);
                }
            }
        }
        foreach ($arrayStories['QueryResult']['Results'] as $result) {
            if (in_array($result['FormattedID'], $artifacts) ) {
                $story = $storyRepo->findOneBy(array(
                    'objectUUID' => $result['ObjectUUID']
                ));
                if (!$story) {
                    $story = new Story();
                    $story->setCode($result['FormattedID']);
                    $story->setName($result['_refObjectName']);
                    $story->setObjectUUID($result['ObjectUUID']);
                    $story->setOwner($result['Owner']['_refObjectName']);
                    $testRun = '';
                    if ( (int) $result['TestCases']['Count']>0) {
                        $testRun = '/testrun';
                    }
                    $url = "https://rally1.rallydev.com/#/" . $projectID . "d/detail/userstory/"
                        .$result['ObjectID'] . $testRun;
                    $story->setRallyUrl($url);
                    $story->setRelease($release);
                    $em->persist($story);
                }
            }
        }
        
        $em->flush();
        
        $json=array();
        return new JsonResponse($json);
    }
    
    /**
    * @Route("/updateStateArtifact", name="ApiRallyUpdateStateArtifact")
    * @Method({"GET"})
    */
    public function updateStateArtifactAction(Request $request) {
        $artifactId = $request->query->get('artifactId');

        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'jleon@alertlogic.com', '2hL}Vo}UgyZ5');
      
        $jsonDefects = $rally->modify(
                'defect/54738273858', 
                array(
                  //"Defect" => array(
                    "PlanEstimate" => 1
                  //)
                  
                )
        );
        
        $arrayDefects = json_decode($jsonDefects, true);
        var_dump($arrayDefects);
        //$arrayStories = json_decode($jsonStories, true);
        
        $json=array();
        return new JsonResponse($json);
    }

}
