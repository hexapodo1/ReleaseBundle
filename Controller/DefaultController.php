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
        
        $dataCenters = $dataCenterRepo->findBy(array(
            'active' => true
        ));
        
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
        $userId = $this->getUser()->getId();
        
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
            $state = "(success)";
        } else {
            $state = "(facepalm) Sorry.";
        }

        $return = array(
            'success' => $success,
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
        
        $dataCenters = $dataCenterRepo->findBy(array(
            'active' => true
        ));
        
        return $this->render('ReleaseBundle:Default:summary.html.twig', array(
            'release' => $release,
            'stories' => $stories,
            'dataCenters' => $dataCenters
        ));
    }
    
    /**
     * @Route("/user", name="user")
     */
    public function userAction() {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ReleaseBundle:User');
        $entity = $repo->find(1);
        $entity->setSalt(md5(time()));
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
        $passwordCodificado = $encoder->encodePassword('123456', $entity->getSalt());
        $entity->setPassword($passwordCodificado);    
        $em->persist($entity);
        $em->flush();
    }
    
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        $hc = $this->get('hipchat');
        $hc->config('https://www.hipchat.com/v2/room/', '2789987', 'kTiVRdxwscMANUunXAzfklP5SlEVS4Dtx3jcC3Je');
        
        $data = array(
            "color" => "green",
            "message" => 'juan leon bazante',
            "notify" => false,
            "message_format" => "html"
        );

        $hc->setData($data);
        $hc->execute();
        $hc->close();
        
        $rally = $this->get('rally');
        $rally->config('https://rally1.rallydev.com/slm/webservice/v2.0/', 'juanbazante@hotmail.com', 'Lgbemyet-1978');
        $json = $rally->execute('release');
        $rally->close();
        
        return new JsonResponse(json_decode($json));
        

    }
}
