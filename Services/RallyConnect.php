<?php
namespace Kishron\ReleaseBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Allow to connect to Rally API
 * @Author Juan Leon
 * 
 */
class RallyConnect {

    private $baseUrl;
    private $username;
    private $password;
    private $key;
    private $curl;

    public function __construct(Container $container) {
        $parameters = $container->getParameter('rally');

        $this->baseUrl = $parameters['baseUrl'];
        $this->username = $parameters['userLogin'];
        $this->password = $parameters['userPassword'];
        $this->key = $parameters['userApi'];
        $this->_init();
    }

    private function _init() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->username . ":" . $this->password);
    }

    public function execute($type = '', $query = '', $order = '', $fetch = '') {
        if (trim($query) === '') {
            $url = $this->baseUrl . $type;
        } else {
            $url = $this->baseUrl . $type . '?query=' . urlencode($query);
        }
        if (trim($order) !== '') {
            $url .= "&order=" . urlencode($order);
        }
        if (trim($fetch) !== '') {
            $url .= "&fetch=" . $fetch;
        }
        //echo $url; exit();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $result = curl_exec($this->curl);
    }
    
    public function sendHeaderJson() {
        ob_start('ob_gzhandler');
        header('Content-type: application/json');
    }

    public function close() {
        curl_close($this->curl);
    }
    
    public function update($id, $params, $type) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'zsessionid:' . $this->key,
            'Content-Type: application/json'
        ));
        $payload = json_encode($params);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($this->curl, CURLOPT_URL, 
            'https://rally1.rallydev.com/slm/webservice/v2.0/' . $type . '/' . $id
        );
        return $result = curl_exec($this->curl);
    }

}
