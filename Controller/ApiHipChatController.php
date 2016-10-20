<?php

namespace Kishron\ReleaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/hc")
 */
class ApiHipChatController extends Controller
{
    /**
    * @Route("/sendMessage", name="ApiHCSendMessage")
    * @Method({"POST"})
    */
    public function sendMessageAction(Request $request) {
        $parameters = $request->request->get('parameters');
        $hipchat = $this->get('hipchat');
        $hipchat->setData($parameters);
        $response = $hipchat->execute();
        $hipchat->close();
        return new JsonResponse($response);
    }
}
