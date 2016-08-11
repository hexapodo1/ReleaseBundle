<?php

namespace Kishron\ReleaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Kishron\ReleaseBundle\Entity\Revision;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        // repos
        $storyRepo = $em->getRepository("ReleaseBundle:Story");
        $releaseRepo = $em->getRepository("ReleaseBundle:ReleaseObj");
        $dataCenterRepo = $em->getRepository("ReleaseBundle:DataCenter");
        
        $release = $releaseRepo->findOneBy(array(
            'active' => true
        ));
        
        $stories = $storyRepo->findBy(array(
            'release' => $release
        ));
        
        $dataCenters = $dataCenterRepo->findAll();
        
        return $this->render('ReleaseBundle:Default:index.html.twig', array(
            'release' => $release,
            'stories' => $stories,
            'dataCenters' => $dataCenters
        ));
    }
    
    /**
     * @Route("/process", name="process")
     */
    public function processAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $action = $request->request->get('action');
        
        $dataCenterId = $request->request->get('dataCenterId');
        $userId = $request->request->get('userId');
        $storyId = $request->request->get('storyId');
        
        $dataCenter = $em->getRepository('ReleaseBundle:DataCenter')->find($dataCenterId);
        $user = $em->getRepository('ReleaseBundle:User')->find($userId);
        $story = $em->getRepository('ReleaseBundle:Story')->find($storyId);
        
        if ($action === 'reviewed') {
            $revision = new Revision();
            $revision
                    ->setDataCenter($dataCenter)
                    ->setStory($story)
                    ->setUser($user)
                    ->setDate(new \DateTime());
            $em->persist($revision);
            $em->flush();
            $response = array(
                'success' => true,
                'message' => 'Story Reviewed.'
            );
        } elseif ($action === 'leave') {
            $revision = $em->getRepository('ReleaseBundle:Revision')->findOneBy(array(
                'dataCenter' => $dataCenterId,
                'story' => $storyId,
                'user' => $userId,
            ));
            $em->remove($revision);
            $em->flush();
            $response = array(
                'success' => true,
                'message' => 'Leave the revision of this story.'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'An error has occurred.'
            );
        }
        
        return new JsonResponse($response);
    }
    
    /**
     * @Route("/reviews", name="reviews")
     */
    public function reviewsAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $userId = 1;
        
        $dataCenterId = $request->request->get('dataCenterId');
        $storyId = $request->request->get('storyId');
        
        $dataCenter = $em->getRepository('ReleaseBundle:DataCenter')->find($dataCenterId);
        $user = $em->getRepository('ReleaseBundle:User')->find($userId);
        $story = $em->getRepository('ReleaseBundle:Story')->find($storyId);
        
        $revisions = $em->getRepository('ReleaseBundle:Revision')->findBy(array(
                'dataCenter' => $dataCenterId,
                'story' => $storyId,
            ));
        $success = false;
        foreach ($revisions as $revision) {
            if ($revision->getUser()->getId() === $user->getId()) {
                $success = $success || true;
            }
        }
        
        if ($success) { 
            $state = "(successful)";
        } else {
            $state = "(facepalm) Sorry.";
        }

        $return = array(
            'n' => count($revisions),
            'message' => $user->getName() 
                . ': ' . $story->getCode() . ' - '
                . $story->getName()
                . ' in ' . $dataCenter->getName()
                . ' ' . $state
        );
        return new JsonResponse($return);
    }
    
    /**
     * @Route("/statusStories", name="statusStories")
     */
    public function statusStoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        // repos
        $revisionRepo = $em->getRepository("ReleaseBundle:Revision");
        $releaseRepo = $em->getRepository("ReleaseBundle:ReleaseObj");
        
        $release = $releaseRepo->findOneBy(array(
            'active' => true
        ));
        $revisions = $revisionRepo->getRevisionsByRelease($release->getId());
        
        $storyCount = array();
        foreach ($revisions as $revision) {
            if (isset($storyCount[$revision->getDataCenter()->getId()][$revision->getStory()->getId()])) {
                $storyCount[$revision->getDataCenter()->getId()][$revision->getStory()->getId()]++;
            } else {
                $storyCount[$revision->getDataCenter()->getId()][$revision->getStory()->getId()] = 1;
            }
        }
        return new JsonResponse($storyCount);
    }
    
    /**
     * @Route("/summary", name="summary")
     */
    public function summaryAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        // repos
        $storyRepo = $em->getRepository("ReleaseBundle:Story");
        $releaseRepo = $em->getRepository("ReleaseBundle:ReleaseObj");
        $dataCenterRepo = $em->getRepository("ReleaseBundle:DataCenter");
        
        $release = $releaseRepo->findOneBy(array(
            'active' => true
        ));
        
        $stories = $storyRepo->findBy(array(
            'release' => $release
        ));
        
        $dataCenters = $dataCenterRepo->findAll();
        
        return $this->render('ReleaseBundle:Default:summary.html.twig', array(
            'release' => $release,
            'stories' => $stories,
            'dataCenters' => $dataCenters
        ));
    }
}
