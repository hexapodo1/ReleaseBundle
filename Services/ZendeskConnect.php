<?php
namespace Kishron\ReleaseBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Allow to connect to Zendesk API
 * @Author Juan Leon
 * 
 */
class ZendeskConnect {

    private $baseUrl;
    private $username;
    private $password;
    private $curl;

    public function __construct(Container $container) {
        $parameters = $container->getParameter('zendesk');

        $this->baseUrl = $parameters['baseUrl'];
        $this->username = $parameters['userLogin'];
        $this->password = $parameters['userPassword'];
        $this->_init();
    }

    private function _init() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->username . ":" . $this->password);
    }

    public function update($id, $params, $type) {
        
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $payload = json_encode($params);
        
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
        $url = $this->baseUrl . $type . '/' . $id . '.json';
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
    
    public function close() {
        curl_close($this->curl);
    }

}
