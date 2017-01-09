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
    * @Route("/updateTickets", name="ApiZendeskUpdateTickets")
    * @Method({"POST"})
    */
    public function updateTicketsAction(Request $request) {
        $tickets = $request->request->get('tickets', array());
        $zendesk = $this->get('zendesk');
        try {
            foreach ($tickets as $ticket) {
                $payload = array(
                    "ticket" => array(
                        "comment" => array(
                            "html_body" => $ticket['message'],
                            "public" => false
                        )
                    )
                );
                $responseJson = $zendesk->updateTicket($ticket['id'], $payload, 'tickets');
            }
            $response = array(
                'success' => true
            );
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'error' => $e->getMessage()
            );
        }
        
        return new JsonResponse($response);
    }
    
}
