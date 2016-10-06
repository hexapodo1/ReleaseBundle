<?php
namespace Kishron\ReleaseBundle\Services;

/**
 * Allow to connect to Rally API
 * @Author Juan Leon
 * 
 */
class RallyConnect {

    private $baseUrl;
    private $username;
    private $password;
    private $curl;

    public function config($baseUrl, $username, $password) {
        $this->baseUrl = $baseUrl;
        $this->username = $username;
        $this->password = $password;
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

}
