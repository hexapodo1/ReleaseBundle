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
 * @Route("/api/rally/v1")
 */
class ApiRallyController extends Controller
{
    /**
     * @Route("/releases", name="ApiRallyReleases")
     * @Method({"GET"})
     */
    public function activeReleasesAction(Request $request)
    {
        $projectId = $request->query->get('projectId');
        $rally = $this->get('rally');
        $json = $rally->execute(
                'release',
                '((Project.ObjectUUID = "' . $projectId . '") and (State = "Active"))',
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
        $projecteRepo = $em->getRepository('ReleaseBundle:Project');
        $releaseRepo = $em->getRepository('ReleaseBundle:ReleaseObj');
        $storyRepo = $em->getRepository('ReleaseBundle:Story');
        
        $rally = $this->get('rally');
        
        $jsonRelease = $rally->execute(
                'release',
                '(ObjectUUID = "' . $releaseId . '")',
                '',
                'Name,ReleaseDate,ObjectID,ObjectUUID,Project'
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
            $projectUUID = $arrayRelease['QueryResult']['Results'][0]['Project']['ObjectUUID'];
            $project = $projecteRepo->findOneBy(array(
                'objectUUID' => $projectUUID
            ));
            $release = new ReleaseObj();
            $release->setActive(1);
            $release->setSuccess(0);
            $release->setCode($arrayRelease['QueryResult']['Results'][0]['Name']);
            $release->setDate(new \DateTime($arrayRelease['QueryResult']['Results'][0]['ReleaseDate']));
            $release->setObjectID($arrayRelease['QueryResult']['Results'][0]['ObjectID']);
            $release->setObjectUUID($arrayRelease['QueryResult']['Results'][0]['ObjectUUID']);
            $release->setProject($project);
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
                    $story->setObjectID($result['ObjectID']);
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
                    $story->setObjectID($result['ObjectID']);
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
    * @Route("/closeArtifacts", name="ApiRallyCloseArtifacts")
    * @Method({"POST"})
    */
    public function closeArtifactsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $releaseRepo = $em->getRepository('ReleaseBundle:ReleaseObj');
        $release = $releaseRepo->findOneBy(array(
            'active' => true
        ));
        $artifacts = $request->request->get('artifacts');
        $rallyConfig = $this->container->getParameter('rally');
        $zendeskField = $rallyConfig['zendeskField'];
        $rally = $this->get('rally');
        $artifactsToUpdate = array();
        foreach($artifacts as $artifact) {
            $artifactArray = explode(':', $artifact);
            $type = substr($artifactArray[0], 0, 2);
            $id = $artifactArray[1];
            if ($type === 'DE') {
                $payload = array(
                    "defect" => array(
                        "c_scrumfield"  => "Released",
                        "ScheduleState" => "Released",
//                        "ScheduleState" => "Accepted",
                        "State"         => "Closed"
                    )
                );
                $response = json_decode($rally->update($id, $payload, 'defect'), true);
                if(array_key_exists('Object', $response['OperationResult'])) {
                    $object = $response['OperationResult']['Object'];
                    $zendeskTicket = array_key_exists($zendeskField, $object) ? trim($object[$zendeskField]) : null;
                    if ($zendeskTicket === null || $zendeskTicket === '') {
                        $zendeskTicketId = null;
                    } else {
                        if (strrpos($zendeskTicket, '/')) {
                            $zendeskTicketId = substr($zendeskTicket, strrpos($zendeskTicket, '/') + 1);
                        } else {
                            $zendeskTicketId = $zendeskTicket;
                        }
                    }
                    $artifactsToUpdate[] = array(
                        'type' => $type,
                        'zendeskTicketId' => $zendeskTicketId, 
                        'code' => $object['FormattedID'],
                        'name' => $object['Name'],
                        'release' => array(
                            'code' => $release->getCode(),
                            'date' => $release->getDate()->format('Y-m-d')
                        )
                    );
                }
            } else {
                $payload = array(
                    "defect" => array(
                        "c_scrumfield"  => "Released",
                        "ScheduleState" => "Released",
//                        "ScheduleState" => "Accepted",
                    )
                );
                $response = json_decode($rally->update($id, $payload, 'hierarchicalrequirement'), true);
                if(array_key_exists('Object', $response['OperationResult'])) {
                    $object = $response['OperationResult']['Object'];
                    $zendeskTicket = array_key_exists($zendeskField, $object) ? trim($object[$zendeskField]) : null;
                    if ($zendeskTicket === null || $zendeskTicket === '') {
                        $zendeskTicketId = null;
                    } else {
                        if (strrpos($zendeskTicket, '/')) {
                            $zendeskTicketId = substr($zendeskTicket, strrpos($zendeskTicket, '/') + 1);
                        } else {
                            $zendeskTicketId = $zendeskTicket;
                        }
                    }
                    $artifactsToUpdate[] = array(
                        'type' => $type,
                        'zendeskTicketId' => $zendeskTicketId,
                        'code' => $object['FormattedID'],
                        'name' => $object['Name'],
                        'release' => array(
                            'code' => $release->getCode(),
                            'date' => $release->getDate()->format('Y-m-d')
                        )
                    );
                }
            }
            
        }
//        ini_set('xdebug.var_display_max_depth', -1);
//            ini_set('xdebug.var_display_max_children', -1);
//            ini_set('xdebug.var_display_max_data', -1);
//        var_dump($asd);
//        exit();
        
        return new JsonResponse($artifactsToUpdate);
    }
    
    /**
    * @Route("/updateStateArtifacts", name="ApiRallyUpdateStateArtifacts")
    * @Method({"GET"})
    */
    public function updateStateArtifactsAction(Request $request) {
        
        $rally = $this->get('rally');

        $jsonDefects = $rally->execute(
                'defect', 
                '(Release.ObjectUUID = "ffa3460d-a94c-4581-b306-4d737b8956e6")', 
                'FormattedID ASC', 
                'ObjectID,FormattedID'
        );
        $jsonStories = $rally->execute(
                'hierarchicalrequirement', 
                '(Release.ObjectUUID = "b35810bb-0299-4d89-bed5-a5114691d71c")', 
                'FormattedID ASC', 
                'ObjectID,FormattedID'
        );
        
        $arrayDefects = json_decode($jsonDefects, true);
        $arrayStories = json_decode($jsonStories, true);
        var_dump($arrayDefects['QueryResult']['Results']);
        exit();
        
        foreach($arrayDefects['QueryResult']['Results'] as $defect) {
            $id = $defect['ObjectID'];
            $payload = array(
                "defect" => array(
                    "c_scrumfield"  => "Released",
                    "ScheduleState" => "Released",
                    "State"         => "Closed"
                )
            );
            $asd = $rally->update($id, $payload, 'defect');
            //var_dump($defect['FormattedID']);
        }
        foreach($arrayStories['QueryResult']['Results'] as $defect) {
            $id = $defect['ObjectID'];
            $payload = array(
                "hierarchicalrequirement" => array(
                    "c_scrumfield"  => "Released",
                    "ScheduleState" => "Released"
                )
            );
            $asd = $rally->update($id, $payload, 'hierarchicalrequirement');
            //var_dump($defect['FormattedID']);
        }
        exit();
        
        return new JsonResponse($json);
    }
    
    /**
     * @Route("/projects", name="ApiRallyProjects")
     * @Method({"GET"})
     */
    public function projectsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $projectsToView = array();
        $projectRepo = $em->getRepository('ReleaseBundle:Project');
        
        $projects = $projectRepo->findAll();
        
        foreach ($projects as $project) {
            $projectsToView[] = array(
                'name' => $project->getName(),
                'objectID' => $project->getObjectID(),
                'objectUUID' => $project->getObjectUUID(),
            );
        }

        return new JsonResponse($projectsToView);
    }

}
