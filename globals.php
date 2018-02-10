<?php
    use Database\DBUtil;
    use Database\DBUtilException;

    include(__DIR__ . '/config.php');

    if(!function_exists('findFile')) {
        function findFile($file, $parents = 3) {
            if(!file_exists($file)) {
                for($i = 0; $i < $parents; $i++) {
                    if(!file_exists($file)) {
                        $file = '../' . $file;
                    } else {
                        return $file;
                    }
                }
            } else {
                return $file;
            }

            return false;
        }
    }

    if(!function_exists('findAutoloader')) {
        function findAutoloader() {
            $autoLoader = 'autoloader.php';
            $autoLoader = findFile($autoLoader, 5);

            if(!$autoLoader) {
                die("Failed to find autoloader");
            }

            return $autoLoader;
        }
    }

    require(findAutoloader());

    try {
        $database = new DBUtil($GLOBALS['dbDetails']);
    } catch(DBUtilException $ex) {
        die($ex->getMessage());
    }

    $GLOBALS['db'] = $database;