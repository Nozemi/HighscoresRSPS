<?php
namespace Endpoint;

abstract class AbstractEndpoint {
    protected $parameters;

    public function __construct($params = '') {
        $this->parameters = explode('/', $params);
    }

    public function getParameter($index) {
        if(isset($this->parameters[$index])) {
            return $this->parameters[$index];
        } else {
            return false;
        }
    }

    public function getParam($index) {
        return strtolower($this->getParameter($index));
    }

    public function getRequestParameter($name) {
        if(isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        } else {
            return false;
        }
    }

    public function getRequest($name) {
        return $this->getRequestParameter($name);
    }
}