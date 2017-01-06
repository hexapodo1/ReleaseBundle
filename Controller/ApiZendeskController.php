<?php

namespace Kishron\ReleaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Kishron\ReleaseBundle\Entity\ReleaseObj;
use Kishron\ReleaseBundle\Entity\Story;

/**
 * @Route("/api/zendesk/v1")
 */
class ApiZendeskController extends Controller
{
    
    
    /**
    * @Route("/updateTicket", name="ApiZendeskUpdateTicket")
    * @Method({"GET"})
    */
    public function updateTicketAction(Request $request) {
        $zendeskConfig = $this->container->getParameter('zendesk');
        if ($zendeskConfig['debug']) {
            ini_set('xdebug.var_display_max_depth', -1);
            ini_set('xdebug.var_display_max_children', -1);
            ini_set('xdebug.var_display_max_data', -1);
        }
        $zendesk = $this->get('zendesk');

        
        $payload = array(
            "ticket" => array(
//                    "status" => "pending",
//                    "priority" => 'low',
                "comment" => array(
                    "html_body" => "thanks 123",
                    "author_id" => 16802009308,
                    "public" => false
                )
            )
        );
        //$responseJson = $zendesk->updateTicket(1, $payload, 'tickets');
        //$responseJson = $zendesk->audit(1);
        $responseJson = $zendesk->showComments(2);
        $response = json_decode($responseJson, true);
        $authors = array(null); // dummy null
        $return = array();
        foreach ($response['comments'] as $r) {
            $return[] = array(
                'created_at' => $r['created_at'],
                'body'       => $r['body'],
                'author_id'  => $r['author_id']
            );
            $authors[] = $r['author_id'];
        }
        $authors = array_unique($authors);
        $strAuthors = implode(',', $authors);
        $responseJson = $zendesk->showUsers($strAuthors);
        $response = json_decode($responseJson, true);
        $users = array();
        if (array_key_exists('users', $response)) {
            foreach ($response['users'] as $user) {
                $users[] = array(
                    'id'   => $user['id'],
                    'name' => $user['name']
                );
            }
        } 
        array_walk($return, function(&$element) use ($users){
            foreach ($users as $user) {
                if (false !== array_search($element['author_id'], $user)) {
                    $element['author_name'] = $user['name'];
                    //break;
                }
            }
        });
        //  var_dump($return);exit();
        return new JsonResponse($return);
    }

}
