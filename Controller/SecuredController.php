<?php

namespace Kishron\ReleaseBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * edit: juan
 * Route("/")
 */

class SecuredController extends Controller
{
    /**
     * edit: Juan
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request){
        
        
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'ReleaseBundle:Login:login.html.twig',
            array(
                // last username entered by the user
                'last_user' => $lastUsername,
                'error'         => $error,
            )
        );
        
        
    }
    
    /**
     * edit: Juan
     * @Route("/login_check", name="login_check")
     */
    public function securityCheckAction(){
         
    }

    /**
     * edit: juan
     * @Route("/logout", name="logout")
     */
    public function webLogoutAction(){
        
    }
}
