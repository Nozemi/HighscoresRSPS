<?php
    use Endpoint\AbstractEndpoint;

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST, GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require(__DIR__ . '/globals.php');

    if(!isset($_REQUEST['endpoint'])) {
        // TODO: Use JsonData message
        die("No endpoint specified. Please specify an endpoint.");
    }

    $endpoint = ucwords($_GET['endpoint']);
    $params   = (isset($_GET['params']) ? $_GET['params'] : null);

    if(strlen($endpoint) <= 0) {
        // TODO: Use JsonData message
        die("You need to specify an endpoint.");
    }

    $endpointHandler = null;

    if(class_exists($endpoint)) {
        $endpointHandler = new $endpoint($params);
    } else if(class_exists('Endpoint\\' . $endpoint)) {
        $className = 'Endpoint\\' . $endpoint;
        $endpointHandler = new $className($params);
    } else {
        http_response_code(404);
        // TODO: Use JsonData message
        echo "Class not found.<br />" . PHP_EOL;
        // Handle error.
    }

    if(!$endpointHandler instanceof AbstractEndpoint) {
        // TODO: Use JsonData message
        echo 'Class: ' . get_class($endpointHandler);
        //die("Endpoint {$endpointHandler} does not extend Endpiont\\AbstractEndpoint.");
    }