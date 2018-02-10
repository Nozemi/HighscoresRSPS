<?php
namespace Endpoint;

abstract class AbstractEndpoint {
    protected $parameters;

    public function __construct($params = '') {
        $this->parameters = explode('/', $params);
    }

    public function getParameter($index) {
        return $this->parameters[$index];
    }

    public function getParam($index) {
        return $this->getParameter($index);
    }
}