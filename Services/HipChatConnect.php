<?php
namespace Kishron\ReleaseBundle\Services;

/**
 * Allow to connect to HipChat API
 * @Author Juan Leon
 * 
 */
class HipChatConnect {

    private $baseUrl;
    private $roomId;
    private $authToken;
    private $curl;

    public function config($baseUrl, $roomId, $authToken) {
        $this->baseUrl = $baseUrl;
        $this->roomId = $roomId;
        $this->authToken = $authToken;
        $this->_init();
    }

    private function _init() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    }

    public function setData($data) {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }

    public function execute() {
        $url = $this->baseUrl . $this->roomId . "/notification?auth_token=" . $this->authToken;
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

}
