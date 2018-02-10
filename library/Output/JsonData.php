<?php
namespace Utilities;

class JsonData {
    protected $message;
    protected $responseCode;
    protected $data;

    public function __construct($responseCode = 0, $message = '', $data = '') {
        $this->message = $message;
        $this->responseCode = $responseCode;
        $this->data = $data;
    }

    public function setResponseCode($code) {
        $this->responseCode = $code;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getMessage() {
        return json_encode([
            'response' => $this->responseCode,
            'message'  => $this->message,
            'data'     => $this->data
        ], JSON_PRETTY_PRINT);
    }
}