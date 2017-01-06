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

    public function updateTicket($id, $params, $type) {
        $this->_prepare('PUT');
        $payload = json_encode($params);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
        $url = $this->baseUrl . $type . '/' . $id . '.json';
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
        
    public function audit($id) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $this->_prepare('GET');
        $url = $this->baseUrl . 'tickets/' . $id . '/audits.json';
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
    
    public function showComments($id) {
        
        $this->_prepare('GET');
        $url = $this->baseUrl . 'tickets/' . $id . '/comments.json';
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
    
    public function showUsers($ids) {
        $this->_prepare('GET');
        $url = $this->baseUrl . 'users/show_many.json?ids={' . $ids . '}';
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
    
    public function close() {
        curl_close($this->curl);
    }
    
    private function _prepare($method) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));        
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
    }

}
