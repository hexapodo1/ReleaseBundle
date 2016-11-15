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
 * @Route("/api/zendesk/v1")
 */
class ApiZendeskController extends Controller
{
    
    /**
    * @Route("/updateTicket", name="ApiZendeskUpdateTicket")
    * @Method({"GET"})
    */
    public function updateTicketAction(Request $request) {
        
        $zendesk = $this->get('zendesk');

        
            $payload = array(
                "ticket" => array(
                    "status" => "open",
//                    "priority" => 'urgent',
//                    "comment" => array(
//                        "body" => "message from UI Release Application (hold1).",
//                        "author_id" => 14591834407,
//                        "public" => false
//                        
//                    )
                )
            );
            $asd = $zendesk->update(3, $payload, 'tickets');
            //var_dump($defect['FormattedID']);
        //exit();
        
        return new JsonResponse($asd);
    }

}
