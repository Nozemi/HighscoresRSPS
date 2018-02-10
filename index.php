<?php
    use Database\DBUtil;
    use Database\DBUtilQuery;
    use Database\DBUtilException;

    use Objects\Player\Player;
    use Objects\Player\Skill as PlayerSkill;

    use Objects\TopList\Skill as TopListSkill;

    use Endpoint\AbstractEndpoint;

    require(__DIR__ . '/globals.php');

    if(!isset($_REQUEST['endpoint'])) {
        die("No endpoint specified. Please specify an endpoint.");
    }

    $endpoint = ucwords($_GET['endpoint']);
    $params   = (isset($_GET['params']) ? $_GET['params'] : null);

    if(strlen($endpoint) <= 0) {
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
        echo "Class not found.<br />" . PHP_EOL;
        // Handle error.
    }

    if(!$endpointHandler instanceof AbstractEndpoint) {
        echo 'Class: ' . get_class($endpointHandler);
        //die("Endpoint {$endpointHandler} does not extend Endpiont\\AbstractEndpoint.");
    }